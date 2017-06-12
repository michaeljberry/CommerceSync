<?php

namespace wmord;

use \Walmart\Order as WalmartOrder;
use wm\walmartclass;

class wmordclass extends walmartclass
{
    /**
     * @param $wm_consumer_key
     * @param $wm_secret_key
     * @param $wm_api_header
     * @return WalmartOrder
     */
    public function construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header){
        $wmorder = new WalmartOrder([
            'consumerId' => $wm_consumer_key,
            'privateKey' => $wm_secret_key,
            'wmConsumerChannelType' => $wm_api_header
        ]);
        return $wmorder;
    }

    /**
     * @param $wm_consumer_key
     * @param $wm_secret_key
     * @param $wm_api_header
     * @param $o
     * @return array
     */
    public function acknowledge_order($wm_consumer_key, $wm_secret_key, $wm_api_header, $o){
        $wmorder = $this->construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header);
        $poId = $o['purchaseOrderId'];
        $orderAcknowledge = $wmorder->acknowledge([
            'purchaseOrderId' => $poId,
        ]);
        return $orderAcknowledge;
    }

    /**
     * @param $wm_consumer_key
     * @param $wm_secret_key
     * @param $ecommerce
     * @param $wm_store_id
     * @param $ibmdata
     * @param $order
     */
    public function get_wm_order($wm_consumer_key, $wm_secret_key, $ecommerce, $wm_store_id, $ibmdata, $order)
    {
        $order_num = $order['purchaseOrderId'];
        $state_code = $order['shippingInfo']['postalAddress']['state'];

        $total_tax = 0;
        $shipping_amount = 0;
        $shipping = 'ZSTD';
        $total_order = 0;

        $buyer_phone = $order['shippingInfo']['phone'];
        $name = explode(' ', $order['shippingInfo']['postalAddress']['name']);
        $first_name = $name[0];
        $last_name = $name[2];

        $address = ucwords(strtolower($order['shippingInfo']['postalAddress']['address1']));
        $address2 = '';
        if (isset($order['shippingInfo']['postalAddress']['address2'])) {
            $address2 = $order['shippingInfo']['postalAddress']['address2'];
        }
        $city = $order['shippingInfo']['postalAddress']['city'];
        $state_id = $ecommerce->stateId($state_code);
        $zip = $order['shippingInfo']['postalAddress']['postalCode'];
        $zip_id = $ecommerce->zipSoi($zip, $state_id);
        $country = $order['shippingInfo']['postalAddress']['country'];

        echo "<br><br>Order: $order_num<br><pre>";
        print_r($order);
        echo '</pre><br><br>';

        if(array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $this->process_orders($order['orderLines']['orderLine'], $total_tax, $shipping_amount, $total_order);
            $order_items = $order['orderLines'];
        }else{
            foreach($order['orderLines']['orderLine'] as $o){
                $this->process_orders($o, $total_tax, $shipping_amount, $total_order);
            }
            $order_items = $order['orderLines']['orderLine'];
        }

        if ($total_order > 299) {
            $shipping = 'URIP';
        }

        $city_id = $ecommerce->citySoi($city, $state_id);
        $cust_id = $ecommerce->customer_soi($first_name,$last_name,ucwords(strtolower($address)),ucwords(strtolower($address2)),$city_id,$state_id,$zip_id);
        $order_id = $ecommerce->save_order($wm_store_id, $cust_id, $order_num, $shipping, $shipping_amount, $total_tax);
        $info_array = $this->get_wm_order_items($wm_consumer_key, $wm_secret_key, $ecommerce, $order_num, $order_items, $state_code, $total_tax, $order_id);
        $item_xml = $info_array['item_xml'];
        $xml = $this->save_wm_order_to_xml($order, $item_xml, $ecommerce, $first_name, $last_name, $shipping, $buyer_phone, $address, $address2, $city, $state_code, $zip, $country, $shipping_amount, $ibmdata);
        $ecommerce->save_xml_to_hd($order_num, $xml, 'Walmart');
    }

    public function process_orders($order, &$total_tax, &$shipping_amount, &$total_order)
    {
//        echo '<br><br>Order Items:<br><pre>';
//        print_r($order);
//        echo '</pre><br><br>';
        if (array_key_exists('tax', $order['charges']['charge'])) {
            foreach ($order['charges'] as $t) {
                $total_tax += number_format($t['tax']['taxAmount']['amount'], 2, '.', '');
//                echo "Taxes: $total_tax <br>";
            }
        }

        foreach ($order['charges'] as $p) {
            $total_order += $p['chargeAmount']['amount'];
        }

        foreach ($order['charges'] as $s) {
            if (in_array('SHIPPING', $s)) {
                foreach ($s['charges'] as $sa) {
                    if ($sa['chargeType'] == 'SHIPPING') {
                        $shipping_amount += number_format($sa['chargeAmount']['amount'], 2, '.', '');
                    }
                }
            }
        }
    }

    /**
     * @param $wm_consumer_key
     * @param $wm_secret_key
     * @param $ecommerce
     * @param $order_num
     * @param $order_items
     * @param $state_code
     * @param $total_tax
     * @param $order_id
     * @return array
     */
    public function get_wm_order_items($wm_consumer_key, $wm_secret_key, $ecommerce, $order_num, $order_items, $state_code, $total_tax, $order_id){
        $wminv = new \wminv\wminvclass();
        $item_xml = '';
        $ponumber = 1;

        foreach($order_items as $i){
//            echo '<br><br><pre>';
//            print_r($i);
//            echo '</pre><br><br>';
            $quantity = $i['orderLineQuantity']['amount'];
            $title = $i['item']['productName'];
            $principle = 0;
            foreach($i['charges'] as $p){
                if($p['chargeType'] == 'PRODUCT'){
                    $principle += $p['chargeAmount']['amount'];
                }
            }
            $item_total = sprintf("%01.2f",number_format($principle, 2, '.', '')/$quantity);
            echo "Item Total: $item_total";
            $sku = $i['item']['sku'];
            $item = $wminv->get_item($wm_consumer_key, $wm_secret_key, $sku);
            $upc = $item['MPItemView']['upc'];
            $sku_id = $ecommerce->skuSoi($sku);
            $ecommerce->save_order_items($order_id, $sku_id, $item_total, $quantity);
            $item_xml .= $ecommerce->create_item_xml($sku, $title, $ponumber, $quantity, $item_total, $upc);
            $ponumber++;
        }
//        if(strtolower($state_code) == 'id' || strtolower($state_code) == 'idaho'){
//            $item_xml .= $ecommerce->create_tax_item_xml($ponumber, number_format($total_tax, 2), 'ID');
//        }elseif(strtolower($state_code) == 'ca' || strtolower($state_code) == 'california'){
//            $item_xml .= $ecommerce->create_tax_item_xml($ponumber, number_format($total_tax, 2), 'CA');
//        }elseif(strtolower($state_code) == 'wa' || strtolower($state_code) == 'washington'){
//            $item_xml .= $ecommerce->create_tax_item_xml($ponumber, number_format($total_tax, 2), 'WA');
//        }
        $item_xml .= $ecommerce->get_tax_item_xml($state_code, $ponumber, $total_tax);
        $info_array = [
            'item_xml' => $item_xml
        ];
        return $info_array;
    }

    /**
     * @param $order
     * @param $item_xml
     * @param $e
     * @param $first_name
     * @param $last_name
     * @param $shipping
     * @param $buyer_phone
     * @param $address
     * @param $address2
     * @param $city
     * @param $state
     * @param $zip
     * @param $country
     * @param $shipping_amount
     * @param $ibmdata
     * @return mixed
     */
    public function save_wm_order_to_xml($order, $item_xml, $e, $first_name, $last_name, $shipping, $buyer_phone, $address, $address2, $city, $state, $zip, $country, $shipping_amount, $ibmdata){
        $sku = $order['orderLines']['orderLine']['item']['sku'];
        $channel_name = 'Walmart';
        $channel_num = $e->get_channel_num($ibmdata, $channel_name, $sku);
        $order_num = $order['purchaseOrderId'];
        $timestamp = $order['orderDate'];
        $timestamp = date("Y-m-d H:i:s", strtotime($timestamp));
        $timestamp = str_replace(' ', 'T', $timestamp);
        $timestamp = $timestamp . '.000Z';
        $order_date = $timestamp;
        $ship_to_name = $first_name . ' ' . $last_name;
        $xml = $e->create_xml($channel_num, $channel_name, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
        return $xml;
    }

    /**
     * @param $wm_consumer_key
     * @param $wm_secret_key
     * @param $wm_api_header
     * @param $order_num
     * @param $tracking_id
     * @param $carrier
     * @return array
     */
    public function update_walmart_tracking($wm_consumer_key, $wm_secret_key, $wm_api_header, $order_num, $tracking_id, $carrier){
        $wmorder = $this->construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header);
        $order = $wmorder->get([
            'purchaseOrderId' => $order_num
        ]);
//        print_r($order);
        if(isset($order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']) && array_key_exists('trackingInfo', $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus'])){
            return $order;
        }
        echo '<br><br>';
        $date = date("Y-m-d")."T".date("H:i:s")."Z";
        echo "Date: $date<br><br>";
//        $order_num = $order['purchaseOrderId'];
        $trackingURL = '';
        if($carrier == 'USPS'){
            $trackingURL = "https://tools.usps.com/go/TrackConfirmAction.action";
        }elseif ($carrier == 'UPS'){
            $trackingURL = "http://wwwapps.ups.com/WebTracking/track";
        }
        if(array_key_exists('lineNumber', $order['orderLines']['orderLine'])){
            $tracking = $this->process_tracking($order['orderLines'], $order_num, $date, $carrier, $tracking_id, $trackingURL, $wm_consumer_key, $wm_secret_key, $wm_api_header);
        }else{
            foreach($order['orderLines']['orderLine'] as $o){
                $tracking = $this->process_tracking($order['orderLines']['orderLine'], $order_num, $date, $carrier, $tracking_id, $trackingURL, $wm_consumer_key, $wm_secret_key, $wm_api_header);
            }
        }
//        foreach ($order['orderLines'] as $o){
//            $lineNumber = $o['lineNumber'];
//            $quantity = $o['orderLineQuantity']['amount'];
//            $wmorder = $this->construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header);
//            try {
//                $tracking = $wmorder->ship(
//                    $order_num,
//                    $this->create_tracking_array($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
//                );
//            }catch(Exception $e){
//                die("There was a problem requesting the data: " . $e->getMessage());
//            }
//            print_r($tracking);
//        }
        return $tracking;
    }

    public function process_tracking($order, $order_num, $date, $carrier, $tracking_id, $trackingURL, $wm_consumer_key, $wm_secret_key, $wm_api_header)
    {
        foreach ($order as $o){
            $lineNumber = $o['lineNumber'];
            $quantity = $o['orderLineQuantity']['amount'];
            $wmorder = $this->construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header);
            try {
                $tracking = $wmorder->ship(
                    $order_num,
                    $this->create_tracking_array($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
                );
            }catch(Exception $e){
                die("There was a problem requesting the data: " . $e->getMessage());
            }
//            print_r($tracking);
        }
        return $tracking;
    }
    public function create_tracking_array($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL){
        $tracking = [
            'orderShipment' => [
                'orderLines' => [
                    [
                        'lineNumber' => $lineNumber,
                        'orderLineStatuses' => [
                            [
                                'status' => 'Shipped',
                                'statusQuantity' => [
                                    'unitOfMeasurement' => 'Each',
                                    'amount' => $quantity
                                ],
                                'trackingInfo' => [
                                    'shipDateTime' => $date,
                                    'carrierName' => [
                                        'carrier' => $carrier
                                    ],
                                    'methodCode' => 'Standard',
                                    'trackingNumber' => $tracking_id,
                                    'trackingURL' => $trackingURL
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $tracking;
    }
}