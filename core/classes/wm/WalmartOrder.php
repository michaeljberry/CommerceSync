<?php

namespace wm;

use ecommerce\Ecommerce;
use models\channels\Address;
use models\channels\SKU;
use \Walmart\Order;

class WalmartOrder extends Walmart
{
    /**
     * @return WalmartOrder
     * @internal param $wm_consumer_key
     * @internal param $wm_secret_key
     * @internal param $wm_api_header
     */
    public function configure()
    {
        $wmorder = new Order([
            'consumerId' => WalmartClient::getConsumerKey(),
            'privateKey' => WalmartClient::getSecretKey(),
            'wmConsumerChannelType' => WalmartClient::getAPIHeader()
        ]);
        return $wmorder;
    }

    /**
     * @param $order
     * @return array
     * @internal param $wm_consumer_key
     * @internal param $wm_secret_key
     * @internal param $wm_api_header
     */
    public function acknowledge_order($order)
    {
        $wmorder = $this->configure();
        $poId = $order['purchaseOrderId'];
        $orderAcknowledge = $wmorder->acknowledge([
            'purchaseOrderId' => $poId,
        ]);
        return $orderAcknowledge;
    }

    /**
     * @param Ecommerce $ecommerce
     * @param $folder
     * @param $order
     * @internal param $wm_store_id
     * @internal param $ibmdata
     * @internal param $wm_consumer_key
     * @internal param $wm_secret_key
     */
    public function get_wm_order(Ecommerce $ecommerce, $folder, $order)
    {
        $orderNum = $order['purchaseOrderId'];
        $stateCode = $order['shippingInfo']['postalAddress']['state'];

        $totalTax = 0;
        $shippingTotal = 0;
        $shippingMethod = 'ZSTD';
        $orderTotal = 0;

        $buyerPhone = $order['shippingInfo']['phone'];
        $name = explode(' ', $order['shippingInfo']['postalAddress']['name']);
        $firstName = $name[0];
        $lastName = $name[2];

        $address = ucwords(strtolower($order['shippingInfo']['postalAddress']['address1']));
        $address2 = '';
        if (isset($order['shippingInfo']['postalAddress']['address2'])) {
            $address2 = $order['shippingInfo']['postalAddress']['address2'];
        }
        $city = $order['shippingInfo']['postalAddress']['city'];
        $stateID = Address::stateId($stateCode);
        $zip = $order['shippingInfo']['postalAddress']['postalCode'];
        $zipID = Address::zipSoi($zip, $stateID);
        $country = $order['shippingInfo']['postalAddress']['country'];

        echo "<br><br>Order: $orderNum<br><pre>";
        print_r($order);
        echo '</pre><br><br>';

        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $this->process_orders($order['orderLines']['orderLine'], $totalTax, $shippingTotal, $orderTotal);
            $orderItems = $order['orderLines'];
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $this->process_orders($o, $totalTax, $shippingTotal, $orderTotal);
            }
            $orderItems = $order['orderLines']['orderLine'];
        }

        if ($orderTotal > 299) {
            $shippingMethod = 'URIP';
        }

        $cityID = Address::citySoi($city, $stateID);
        $custumerID = $ecommerce->customer_soi($firstName, $lastName, ucwords(strtolower($address)), ucwords(strtolower($address2)), $cityID, $stateID, $zipID);
        if (!LOCAL) {
            $orderID = $ecommerce->save_order(WalmartClient::getStoreID(), $custumerID, $orderNum, $shippingMethod, $shippingTotal, $totalTax);
        }
        $infoArray = $this->get_wm_order_items($ecommerce, $orderNum, $orderItems, $stateCode, $totalTax, $orderID);
        $itemXml = $infoArray['item_xml'];
        $orderXml = $this->save_wm_order_to_xml($order, $itemXml, $ecommerce, $firstName, $lastName, $shippingMethod, $buyerPhone, $address, $address2, $city, $stateCode, $zip, $country, $shippingTotal);
        $channelName = 'Walmart';
        if (!LOCAL) {
            $ecommerce->saveXmlToFTP($orderNum, $orderXml, $folder, $channelName);
        }
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
    public function get_wm_order_items(Ecommerce $ecommerce, $order_num, $order_items, $state_code, $total_tax, $order_id)
    {
        $wminv = new WalmartInventory();
        $item_xml = '';
        $ponumber = 1;

        foreach ($order_items as $i) {
//            echo '<br><br><pre>';
//            print_r($i);
//            echo '</pre><br><br>';
            $quantity = $i['orderLineQuantity']['amount'];
            $title = $i['item']['productName'];
            $principle = 0;
            foreach ($i['charges'] as $p) {
                if ($p['chargeType'] == 'PRODUCT') {
                    $principle += $p['chargeAmount']['amount'];
                }
            }
            $item_total = sprintf("%01.2f", number_format($principle, 2, '.', '') / $quantity);
            echo "Item Total: $item_total";
            $sku = $i['item']['sku'];
            $item = $wminv->getItem($sku);
            $upc = $item['MPItemView']['upc'];
            $sku_id = SKU::searchOrInsert($sku);
            if (!LOCAL) {
                $ecommerce->save_order_items($order_id, $sku_id, $item_total, $quantity);
            }
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
     * @param $ecommerce
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
    public function save_wm_order_to_xml($order, $item_xml, Ecommerce $ecommerce, $first_name, $last_name, $shipping, $buyer_phone, $address, $address2, $city, $state, $zip, $country, $shipping_amount)
    {
        $sku = $order['orderLines']['orderLine']['item']['sku'];
        $channel_name = 'Walmart';
        $channel_num = $ecommerce->get_channel_num($channel_name, $sku);
        $order_num = $order['purchaseOrderId'];
        $timestamp = $order['orderDate'];
        $timestamp = date("Y-m-d H:i:s", strtotime($timestamp));
        $timestamp = str_replace(' ', 'T', $timestamp);
        $timestamp = $timestamp . '.000Z';
        $order_date = $timestamp;
        $ship_to_name = $first_name . ' ' . $last_name;
        $xml = $ecommerce->create_xml($channel_num, $channel_name, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
        return $xml;
    }

    /**
     * @param $order_num
     * @param $tracking_id
     * @param $carrier
     * @return array
     * @internal param $wm_consumer_key
     * @internal param $wm_secret_key
     * @internal param $wm_api_header
     */
    public function updateWalmartTracking($order_num, $tracking_id, $carrier)
    {
        $wmorder = $this->configure();
        $order = $wmorder->get([
            'purchaseOrderId' => $order_num
        ]);
//        print_r($order);
        if (isset($order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']) && array_key_exists('trackingInfo', $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus'])) {
            return $order;
        }
        echo '<br><br>';
        $date = date("Y-m-d") . "T" . date("H:i:s") . "Z";
        echo "Date: $date<br><br>";
//        $order_num = $order['purchaseOrderId'];
        $trackingURL = '';
        if ($carrier == 'USPS') {
            $trackingURL = "https://tools.usps.com/go/TrackConfirmAction.action";
        } elseif ($carrier == 'UPS') {
            $trackingURL = "http://wwwapps.ups.com/WebTracking/track";
        }
        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $tracking = $this->process_tracking($order['orderLines'], $order_num, $date, $carrier, $tracking_id, $trackingURL);
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $tracking = $this->process_tracking($order['orderLines']['orderLine'], $order_num, $date, $carrier, $tracking_id, $trackingURL);
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

    public function process_tracking($order, $order_num, $date, $carrier, $tracking_id, $trackingURL)
    {
        foreach ($order as $o) {
            $lineNumber = $o['lineNumber'];
            $quantity = $o['orderLineQuantity']['amount'];
            $wmorder = $this->configure();
            try {
                $tracking = $wmorder->ship(
                    $order_num,
                    $this->create_tracking_array($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
                );
            } catch (Exception $e) {
                die("There was a problem requesting the data: " . $e->getMessage());
            }
//            print_r($tracking);
        }
        return $tracking;
    }

    public function create_tracking_array($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
    {
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

    protected function parseOrder($order, $ecommerce, WalmartOrder $wmord, $folder)
    {
//    \ecommerceclass\ecommerceclass::dd($o);
        $order_num = $order['purchaseOrderId'];
        echo "Order: $order_num<br><br>";
        $found = Ecommerce::orderExists($order_num);
        if (!$found) {
            if (!LOCAL) {
                $acknowledged = $wmord->acknowledge_order($order);
            }
//        echo 'Acknowledgement: <br><pre>';
//        print_r($acknowledged);
//        echo '</pre><br><br>';
            if ((array_key_exists('orderLineStatuses', $acknowledged['orderLines']['orderLine']) &&
                    $acknowledged['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['status'] == 'Acknowledged')
                || $acknowledged['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['status'] == 'Acknowledged'
            ) {
                $wmord->get_wm_order($ecommerce, $folder, $order);
            }
        }
    }

    public function getOrders($wmorder, $ecommerce, $wmord, $folder, $next = null)
    {
        try {
            $fromDate = '-3 days';

            if (!empty($next)) {
                $orders = $wmorder->list([
                    'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                    'nextCursor' => $next
                ]);
            } else {
                $orders = $wmorder->listAll([
                    'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
//                'limit' => 200
                ]);
            }

            echo 'Orders: <br>';
            $totalCount = $orders['meta']['totalCount'];
            echo 'Order Count: ' . count($orders['elements']) . '<br><br>';


            if (count($orders['elements']['order']) > 1) { // if there are multiple orders to pull **DO NOT CHANGE**
                foreach ($orders['elements']['order'] as $order) {
                    $this->parseOrder($order, $ecommerce, $wmord, $folder);
                }
            } else {
                foreach ($orders['elements'] as $order) {
                    $this->parseOrder($order, $ecommerce, $wmord, $folder);
                }
            }
//        if($totalCount > 10){ // && !empty($nextCursor)
//            getOrders($wmorder, $db, $ecommerce, $wmord, $folder, $nextCursor); //$nextCursor
//        }
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }
}