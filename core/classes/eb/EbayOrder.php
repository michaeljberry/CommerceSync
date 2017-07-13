<?php

namespace eb;

use controllers\channels\FTPController;
use ecommerce\Ecommerce;
use models\channels\address\Address;
use models\channels\address\State;
use models\channels\Buyer;
use models\channels\Channel;
use models\channels\FTP;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use models\channels\order\OrderItemXML;
use models\channels\order\OrderXML;
use models\channels\Shipping;
use models\channels\SKU;
use models\channels\Tax;
use controllers\channels\TaxXMLController;

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

    public function update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id)
    {
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

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    protected function saveItems($item, $poNumber, $order_id, Ecommerce $ecommerce, $itemObject)
    {
        $sku = $item->Item->SKU;
        $title = $item->Item->Title;
        $quantity = $item->QuantityPurchased;
        $upc = '';
        $principle = Ecommerce::removeCommasInNumber((float)$item->TransactionPrice);
        $item_id = $item->Item->ItemID . '-' . $item->TransactionID;
        $sku_id = SKU::searchOrInsert($sku);
        if (!LOCAL) {
            OrderItem::save($order_id, $sku_id, $principle, $quantity, $item_id);
        }
        $itemXml = OrderItemXML::create($sku, $title, $poNumber, $quantity, $principle, $upc);
        $itemObject['sku'] = $sku;
        $itemObject['itemXml'] .= $itemXml;
        $poNumber++;
        $itemObject['poNumber'] = $poNumber;
        return $itemObject;
    }

    protected function getItems($items, $order_id, Ecommerce $ecommerce)
    {
        $poNumber = 1;

        $itemObject = [];
        $itemObject['itemXml'] = '';

        if (count($items->Transaction) > 1) {
            foreach ($items->Transaction as $item) {
                $itemObject = $this->saveItems($item, $poNumber, $order_id, $ecommerce, $itemObject);
                $poNumber = $itemObject['poNumber'];
            }
        } else {
            $itemObject = $this->saveItems($items->Transaction, $poNumber, $order_id, $ecommerce, $itemObject);
        }

        return (object)$itemObject;
    }

    protected function getMoreOrders($requestName, $pagenumber, $ebayDays)
    {
        $xml = $this->getOrderXml($ebayDays, $pagenumber);
        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
    }

    protected function parseOrders($xml_orders, $folder, Ecommerce $ecommerce)
    {
        foreach ($xml_orders->OrderArray->Order as $xml) {
            $order_num = (string)$xml->ExternalTransaction->ExternalTransactionID;
            $fee = Ecommerce::formatMoney((float)$xml->ExternalTransaction->FeeOrCreditAmount);

            $order_status = trim($xml->OrderStatus);

            if ($order_status !== 'Cancelled') {

                echo "Order: $order_num -> Status: $order_status<br>";
                echo $xml->OrderID . '<br>';
                $found = Order::get($order_num);

                if (!$found) {
                    Ecommerce::dd($xml);
                    $timestamp = $xml->CreatedTime;
                    $order_date = $timestamp;
                    $ismultilegshipping = $xml->IsMultiLegShipping;
                    if (strcasecmp($ismultilegshipping, 'true') == 0) {
                        $shippinginfo = $xml->MultiLegShippingDetails->SellerShipmentToLogisticsProvider->ShipToAddress;
                        $address = strtoupper($shippinginfo->ReferenceID);
                        $address2 = strtoupper($shippinginfo->Street1);
                    } else {
                        $shippinginfo = $xml->ShippingAddress;
                        $address = strtoupper($shippinginfo->Street1);
                        if (is_object($shippinginfo->Street2)) {
                            $address2 = '';
                        } else {
                            $address2 = strtoupper($shippinginfo->Street2);
                        }
                    }
                    $buyer_phone = $shippinginfo->Phone;
                    $ship_to_name = strtoupper($shippinginfo->Name);
                    $city = strtoupper($shippinginfo->CityName);
                    $state = $shippinginfo->StateOrProvince;
                    $zip = $shippinginfo->PostalCode;
                    $country = $shippinginfo->Country;
                    if ($country == 'US') {
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

                    $shipping = Shipping::code($total, $erlanger);

                    $item_taxes = Ecommerce::formatMoney((float)$xml->ShippingDetails->SalesTax->SalesTaxAmount);
                    Ecommerce::dd($item_taxes);
                    $trans_id = $xml->ShippingDetails->SellingManagerSalesRecordNumber;

                    //Get Info into DB
                    $name = explode(' ', $ship_to_name);
                    $last_name = ucwords(strtolower(array_pop($name)));
                    $first_name = ucwords(strtolower(implode(' ', $name)));
                    $state_id = State::getIdByAbbr($state);
                    $zip_id = Address::searchOrInsertZip($zip, $state_id);
                    $city_id = Address::searchOrInsertCity($city, $state_id);
                    $cust_id = Buyer::searchOrInsert($first_name, $last_name, ucwords(strtolower($address)),
                        ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                    if (!LOCAL) {
                        $order_id = Order::save(EbayClient::getStoreID(), $cust_id, $order_num, $shipping,
                            $shipping_amount, $item_taxes, $fee, $trans_id);
                    }

                    $items = $this->getItems($xml->TransactionArray, $order_id, $ecommerce);

                    $poNumber = (string)$items->poNumber;
                    $sku = (string)$items->sku;
                    $itemXml = (string)$items->itemXml;

                    $itemXml .= TaxXMLController::getItemXml($state, $poNumber, $item_taxes);
                    $channelName = 'Ebay';
                    $channel_num = Channel::getAccountNumbersBySku($channelName, $sku);
                    $orderXml = OrderXML::create($channel_num, $channelName, $order_num, $timestamp, $shipping_amount, $shipping, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $itemXml);
                    Ecommerce::dd($orderXml);
                    if (!LOCAL) {
                        FTP::saveXml($order_num, $orderXml, $folder, $channelName);
                    }
                }
            }
        }
    }

    public function getOrders($requestName, $pagenumber, $ebayDays, $folder, Ecommerce $ecommerce)
    {
        $response = $this->getMoreOrders($requestName, $pagenumber, $ebayDays);
        if ($response) {
            $xml_orders = simplexml_load_string($response);
            $orderCount = count($xml_orders->OrderArray->Order);
            echo "Order Count: $orderCount<br>";
            $this->parseOrders($xml_orders, $folder, $ecommerce);
            if ($orderCount >= 100) {
                $pagenumber++;
                $this->getOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce);
            }
        }
    }
}