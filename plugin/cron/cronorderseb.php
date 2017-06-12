<?php
error_reporting(-1);
include __DIR__ . '/../../core/init.php';
include WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'eb/ebvar.php';

use ecommerceclass\ecommerceclass as ecom;

$start_time = microtime(true);
$user_id = 838;
$company_id = 1;

$ebayDays = $ebord->get_order_days($ebord->eb_store_id);

$folder = '/home/chesbro_amazon/';

function saveItems($item, &$ponumber, $order_id, $ecommerce, &$sku){
    $ponumber++;
    $sku = $item->Item->SKU;
    $title = $item->Item->Title;
    $quantity = $item->QuantityPurchased;
    $upc = '';
    $principle = $ecommerce->removeCommasInNumber((float)$item->TransactionPrice);
    $item_id = $item->Item->ItemID . '-' . $item->TransactionID;
    $sku_id = $ecommerce->skuSoi($sku);
    $ecommerce->save_order_items($order_id, $sku_id, $principle, $quantity, $item_id);
    $item_xml = $ecommerce->create_item_xml($sku, $title, $ponumber, $quantity, $principle, $upc);
    return $item_xml;
}

function getItems($items, &$ponumber, $order_id, $ecommerce, &$sku) {
    $item_xml = '';
    if(count($items->Transaction) > 1){
        foreach($items->Transaction as $item){
            $item_xml .= saveItems($item, $ponumber, $order_id, $ecommerce, $sku);
        }
    }else{
        $item_xml .= saveItems($items->Transaction, $ponumber, $order_id, $ecommerce, $sku);
    }
    return $item_xml;
}

function getMoreOrders($requestName, $pagenumber, $ebayDays, $ebord){
    $xml = $ebord->getOrderXml($ebayDays, $pagenumber);
    $response = $ebord->ebayCurl($requestName, $xml);
    return $response;
}

function parseOrders($xml_orders, $folder, $ecommerce, $ibmdata, $ebord){
    foreach($xml_orders->OrderArray->Order as $xml)
    {
        $order_num = (string)$xml->ExternalTransaction->ExternalTransactionID;
        $fee = ecom::formatMoney((float)$xml->ExternalTransaction->FeeOrCreditAmount);

        $order_status = trim($xml->OrderStatus);

        if ($order_status !== 'Cancelled')
        {

            echo "Order: $order_num -> Status: $order_status<br>";
            echo $xml->OrderID . '<br>';
            $found = $ecommerce->orderExists($order_num);

            if (!$found)
            {
                \ecommerceclass\ecommerceclass::dd($xml);
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
                $shipping_amount = ecom::formatMoney((float)$xml->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
                $total = $xml->Total;

                $erlanger = [
                    'address2' => '1850 Airport',
                    'city' => 'Erlanger',
                    'state' => 'KY',
                    'zip' => '41025'
                ];

                $shipping = $ecommerce->shippingCode($total, $erlanger);

                $item_taxes = ecom::formatMoney((float)$xml->ShippingDetails->SalesTax->SalesTaxAmount);
                \ecommerceclass\ecommerceclass::dd($item_taxes);
                $trans_id = $xml->ShippingDetails->SellingManagerSalesRecordNumber;

                //Get Info into DB
                $name = explode(' ', $ship_to_name);
                $last_name = ucwords(strtolower(array_pop($name)));
                $first_name = ucwords(strtolower(implode(' ', $name)));
                $state_id = $ecommerce->stateId($state);
                $zip_id = $ecommerce->zipSoi($zip, $state_id);
                $city_id = $ecommerce->citySoi($city, $state_id);
                $cust_id = $ecommerce->customer_soi($first_name, $last_name, ucwords(strtolower($address)), ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                $order_id = $ecommerce->save_order($ebord->eb_store_id, $cust_id, $order_num, $shipping, $shipping_amount, $item_taxes, $fee, $trans_id);
                $sku = '';

                $item_xml = getItems($xml->TransactionArray, $ponumber, $order_id, $ecommerce, $sku);
                $ponumber++;
                $item_xml .= $ecommerce->get_tax_item_xml($state, $ponumber, $item_taxes);
                $channel_name = 'Ebay';
                $channel_num = $ecommerce->get_channel_num($ibmdata, $channel_name, $sku);
                $orderXml = $ecommerce->create_xml($channel_num, $channel_name, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
//                \ecommerceclass\ecommerceclass::dd($orderXml);
                $ecommerce->saveXmlToFTP($order_num, $orderXml, $folder, 'Ebay');
            }
        }
    }
}

function retrieveOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $ebord, $ibmdata){
    $response = getMoreOrders($requestName, $pagenumber, $ebayDays, $ebord);
    if ($response)
    {
        $xml_orders = simplexml_load_string($response);
        $orderCount = count($xml_orders->OrderArray->Order);
        echo "Order Count: $orderCount<br>";
        parseOrders($xml_orders, $folder, $ecommerce, $ibmdata, $ebord);
        if($orderCount >= 100){
            $pagenumber++;
            retrieveOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $ebord, $ibmdata);
        }
    }
}

$pagenumber = 1;
$requestName = 'GetOrders';

retrieveOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $ebord, $ibmdata);