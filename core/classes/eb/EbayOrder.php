<?php

namespace eb;

use ecommerce\Ecommerce;

class EbayOrder extends Ebay
{

    protected $orderNumber;
    protected $channel;


    public function getOrderXml($ebayDays, $pagenumber)
    {
        $xml = [
            'NumberOfDays' => $ebayDays,
            'Pagination' => [
                'EntriesPerPage' => '100',
                'PageNumber' => $pagenumber
            ],
            'DetailLevel' => 'ReturnAll'
        ];
        return $xml;
    }

    public function update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id){
        $requestName = 'CompleteSale';

        $xml = [
            'ItemID' => $item_id,
            'TransactionID' => $trans_id,
            'Shipped' => 'true',
            'Shipment' =>
            [
                'ShipmentTrackingDetails' =>
                [
                    'ShipmentTrackingNumber' => $tracking_id,
                    'ShippingCarrierUsed' => $carrier
                ]
            ]
        ];

        $response = $this->EbayClient->ebayCurl($requestName, $xml);
        return $response;
    }
    protected function saveItems($item, $poNumber, $order_id, $ecommerce, $itemObject){
        $sku = $item->Item->SKU;
        $title = $item->Item->Title;
        $quantity = $item->QuantityPurchased;
        $upc = '';
        $principle = Ecommerce::removeCommasInNumber((float)$item->TransactionPrice);
        $item_id = $item->Item->ItemID . '-' . $item->TransactionID;
        $sku_id = $ecommerce->skuSoi($sku);
        if (!LOCAL) {
            $ecommerce->save_order_items($order_id, $sku_id, $principle, $quantity, $item_id);
        }
        $itemXml = $ecommerce->create_item_xml($sku, $title, $poNumber, $quantity, $principle, $upc);
        $itemObject['sku'] = $sku;
        $itemObject['itemXml'] .= $itemXml;
        $itemObject['poNumber'] = $poNumber;
        return $itemObject;
    }

    protected function getItems($items, $order_id, $ecommerce) {
        $poNumber = 1;

        $itemObject = [];
        $itemObject['itemXml'] = '';

        if(count($items->Transaction) > 1){
            foreach($items->Transaction as $item){
                $itemObject = $this->saveItems($item, $poNumber, $order_id, $ecommerce, $itemObject);
                $poNumber = $itemObject['poNumber'];
                $poNumber++;
            }
        }else{
            $itemObject = $this->saveItems($items->Transaction, $poNumber, $order_id, $ecommerce, $itemObject);
        }

        return (object)$itemObject;
    }

    protected function getMoreOrders($requestName, $pagenumber, $ebayDays, $EbayClient){
        $xml = $this->getOrderXml($ebayDays, $pagenumber);
        $response = $this->EbayClient->ebayCurl($requestName, $xml);
        return $response;
    }

    protected function parseOrders($xml_orders, $folder, $ecommerce, $ibmdata, $EbayClient){
        foreach($xml_orders->OrderArray->Order as $xml)
        {
            $order_num = (string)$xml->ExternalTransaction->ExternalTransactionID;
            $fee = Ecommerce::formatMoney((float)$xml->ExternalTransaction->FeeOrCreditAmount);

            $order_status = trim($xml->OrderStatus);

            if ($order_status !== 'Cancelled')
            {

                echo "Order: $order_num -> Status: $order_status<br>";
                echo $xml->OrderID . '<br>';
                $found = Ecommerce::orderExists($order_num);

                if (!$found) {
                    Ecommerce::dd($xml);
                    $timestamp = $xml->CreatedTime;
                    $order_date = $timestamp;
                    $ismultilegshipping = $xml->IsMultiLegShipping;
                    if (strcasecmp($ismultilegshipping, 'true') == 0)
                    {
                        $shippinginfo = $xml->MultiLegShippingDetails->SellerShipmentToLogisticsProvider->ShipToAddress;
                        $address = strtoupper($shippinginfo->ReferenceID);
                        $address2 = strtoupper($shippinginfo->Street1);
                    }
                    else
                    {
                        $shippinginfo = $xml->ShippingAddress;
                        $address = strtoupper($shippinginfo->Street1);
                        if(is_object($shippinginfo->Street2))
                        {
                            $address2 = '';
                        }
                        else
                        {
                            $address2 = strtoupper($shippinginfo->Street2);
                        }
                    }
                    $buyer_phone = $shippinginfo->Phone;
                    $ship_to_name = strtoupper($shippinginfo->Name);
                    $city = strtoupper($shippinginfo->CityName);
                    $state = $shippinginfo->StateOrProvince;
                    $zip = $shippinginfo->PostalCode;
                    $country = $shippinginfo->Country;
                    if ($country == 'US')
                    {
                        $country = 'USA';
                    }
                    $shipping_amount = Ecommerce::formatMoney((float)$xml->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
                    $total = $xml->Total;

                    $erlanger = [
                        'address2' => '1850 Airport',
                        'city' => 'Erlanger',
                        'state' => 'KY',
                        'zip' => '41025'
                    ];

                    $shipping = $ecommerce->shippingCode($total, $erlanger);

                    $item_taxes = Ecommerce::formatMoney((float)$xml->ShippingDetails->SalesTax->SalesTaxAmount);
                    Ecommerce::dd($item_taxes);
                    $trans_id = $xml->ShippingDetails->SellingManagerSalesRecordNumber;

                    //Get Info into DB
                    $name = explode(' ', $ship_to_name);
                    $last_name = ucwords(strtolower(array_pop($name)));
                    $first_name = ucwords(strtolower(implode(' ', $name)));
                    $state_id = $ecommerce->stateId($state);
                    $zip_id = $ecommerce->zipSoi($zip, $state_id);
                    $city_id = $ecommerce->citySoi($city, $state_id);
                    $cust_id = $ecommerce->customer_soi($first_name, $last_name, ucwords(strtolower($address)), ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                    if (!LOCAL) {
                        $order_id = $ecommerce->save_order($EbayClient->getStoreID(), $cust_id, $order_num, $shipping, $shipping_amount, $item_taxes, $fee, $trans_id);
                    }

                    $items = $this->getItems($xml->TransactionArray, $order_id, $ecommerce);

                    $poNumber = (string)$items->poNumber;
                    $sku = (string)$items->sku;
                    $itemXml = (string)$items->itemXml;

                    $itemXml .= Ecommerce::get_tax_item_xml($state, $poNumber, $item_taxes);
                    $channelName = 'Ebay';
                    $channel_num = $ecommerce->get_channel_num($ibmdata, $channelName, $sku);
                    $orderXml = $ecommerce->create_xml($channel_num, $channelName, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $itemXml);
                    Ecommerce::dd($orderXml);
                    if (!LOCAL) {
                        $ecommerce->saveXmlToFTP($order_num, $orderXml, $folder, $channelName);
                    }
                }
            }
        }
    }

    public function retrieveOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $EbayClient, $ibmdata){
        $response = $this->getMoreOrders($requestName, $pagenumber, $ebayDays, $EbayClient);
        if ($response)
        {
            $xml_orders = simplexml_load_string($response);
            $orderCount = count($xml_orders->OrderArray->Order);
            echo "Order Count: $orderCount<br>";
            $this->parseOrders($xml_orders, $folder, $ecommerce, $ibmdata, $EbayClient);
            if($orderCount >= 100){
                $pagenumber++;
                $this->retrieveOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $EbayClient, $ibmdata);
            }
        }
    }
}