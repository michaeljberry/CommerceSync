<?php

namespace wm;

use ecommerce\Ecommerce;
use Exception;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;
use controllers\channels\BuyerController;
use controllers\channels\order\OrderItemXMLController;
use controllers\channels\order\OrderXMLController;
use controllers\channels\tax\TaxXMLController;
use \Walmart\Order as WMOrder;

class WalmartOrder extends Walmart
{

    private static $limit = 10;

    public function configure()
    {
        return new WMOrder([
            'consumerId' => WalmartClient::getConsumerKey(),
            'privateKey' => WalmartClient::getSecretKey(),
            'wmConsumerChannelType' => WalmartClient::getAPIHeader()
        ]);
    }

    public function updateWalmartTracking($order_num, $tracking_id, $carrier)
    {
        $wmorder = $this->configure();
        $order = $wmorder->get([
            'purchaseOrderId' => $order_num
        ]);
//        print_r($order);
        if (isset($order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo']) && array_key_exists('trackingInfo',
                $order['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus'])
        ) {
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
        Ecommerce::dd($order);
        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $tracking = $this->processTracking($order['orderLines'], $order_num, $date, $carrier, $tracking_id,
                $trackingURL);
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $tracking = $this->processTracking($order['orderLines']['orderLine'], $order_num, $date, $carrier,
                    $tracking_id, $trackingURL);
            }
        }

        return $tracking;
    }

    public function processTracking($order, $order_num, $date, $carrier, $tracking_id, $trackingURL)
    {
        foreach ($order as $o) {
            $lineNumber = $o['lineNumber'];
            $quantity = $o['orderLineQuantity']['amount'];
            $wmorder = $this->configure();
            try {
                $tracking = $wmorder->ship(
                    $order_num,
                    $this->createTrackingArray($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
                );
            } catch (Exception $e) {
                die("There was a problem requesting the data: " . $e->getMessage());
            }
            print_r($tracking);
        }
        return $tracking;
    }

    public function createTrackingArray($lineNumber, $quantity, $date, $carrier, $tracking_id, $trackingURL)
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

    public function acknowledgeOrder($orderNum)
    {
        return $this->configure()->acknowledge([
            'purchaseOrderId' => $orderNum,
        ]);
    }

    protected function isMulti($array): bool
    {
        foreach ($array as $key => $value){
            if(is_numeric($key)){
                return true;
            }
        }
        return false;
    }

    protected function isAcknowledged($orderLine): bool
    {
        if (!$this->isMulti($orderLine)) {
            return (array_key_exists(
                'orderLineStatuses',
                $orderLine) &&
                $orderLine['orderLineStatuses']['orderLineStatus']['status']
                == 'Acknowledged') ? true : false;
        }
        return (array_key_exists(
            'orderLineStatuses',
            $orderLine[0]) &&
            $orderLine[0]['orderLineStatuses']['orderLineStatus']['status']
            == 'Acknowledged') ? true : false;
    }

    public function getMoreOrders($next)
    {
        try {
            $wmorder = $this->configure();

            $orders = $wmorder->listAll([
                'nextCursor' => $next
            ]);
            Ecommerce::dd($orders);
            $this->parseOrders($wmorder, $orders);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    public function getOrder($orderNum)
    {
        try {
            $wmorder = $this->configure();
            $order = $wmorder->get([
                'purchaseOrderId' => $orderNum
            ]);
            return $order;
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }

    }

    public function getOrders()
    {
        try {
            $wmorder = $this->configure();
            $fromDate = '-' . Walmart::getApiOrderDays() . ' days';

            $orders = $wmorder->listAll([
                'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                'limit' => WalmartOrder::$limit
            ]);
            Ecommerce::dd($orders);
            $this->parseOrders($wmorder, $orders);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    /**
     * @param $wmorder
     * @param $orders
     * @internal param $wmord
     */
    public function parseOrders(WMOrder $wmorder, $orders)
    {
        $totalCount = $orders['meta']['totalCount'];

        if ($this->isMulti($orders['elements'])) { // if there are multiple orders to pull **DO NOT CHANGE**
            echo "Multiple Orders<br>";
            foreach ($orders['elements']['order'] as $order) {
                $this->parseOrder($order);
            }
        } else {
            echo "Single Order:<br>";
            foreach ($orders['elements'] as $order) {
                $this->parseOrder($order);
            }
        }

        if($totalCount > WalmartOrder::$limit){
            $nextCursor = $orders['meta']['nextCursor'];
            $orders = $this->getMoreOrders($nextCursor);

            $this->parseOrders($wmorder, $orders);
        }
    }

    public function parseOrder($order)
    {
        $orderNum = $order['purchaseOrderId'];

        $found = Order::get($orderNum);
        Ecommerce::dd($order);
        if ($this->isAcknowledged($order['orderLines']['orderLine'])) {
            Ecommerce::dd('This order has been acknowledged.');
        }
        if (LOCAL || !$found) {
            $this->orderFound($order, $orderNum);
        }
    }

    protected function orderFound($order, $orderNum)
    {
        if (!LOCAL) {
            $acknowledged = $this->acknowledgeOrder($orderNum);
            Ecommerce::dd($acknowledged);
        }
        if ($this->isAcknowledged($order['orderLines']['orderLine'])) {
//                $this->get_wm_order($order);
        }
    }

    public function get_wm_order($order)
    {
        $orderNum = $order['purchaseOrderId'];
        $channelName = 'Walmart';
        $purchaseDate = (string)$order['orderDate'];

        $tax = 0;
        $shippingPrice = 0;
        $shippingCode = 'ZSTD';
        $orderTotal = 0;

        echo "<br><br>Order: $orderNum<br><pre>";
        print_r($order);
        echo '</pre><br><br>';

        if (array_key_exists('lineNumber', $order['orderLines']['orderLine'])) {
            $orderInfo = $this->process_orders($order['orderLines']['orderLine'], $tax, $shippingPrice, $orderTotal);
            $tax += $orderInfo['total_tax'];
            $shippingPrice += $orderInfo['shipping_total'];
            $orderTotal += $orderInfo['order_total'];

            $orderItems = $order['orderLines'];
        } else {
            foreach ($order['orderLines']['orderLine'] as $o) {
                $orderInfo = $this->process_orders($o, $tax, $shippingPrice, $orderTotal);
                $tax += $orderInfo['total_tax'];
                $shippingPrice += $orderInfo['shipping_total'];
                $orderTotal += $orderInfo['order_total'];
            }
            $orderItems = $order['orderLines']['orderLine'];
        }

        if ($orderTotal > 299) {
            $shippingCode = 'URIP';
        }

        //Address
        $streetAddress = (string)$order['shippingInfo']['postalAddress']['address1'];
        $streetAddress2 = (string)$order['shippingInfo']['postalAddress']['address2'] ?? '';
        $city = (string)$order['shippingInfo']['postalAddress']['city'];
        $state = (string)$order['shippingInfo']['postalAddress']['state'];
        $zipCode = (string)$order['shippingInfo']['postalAddress']['postalCode'];
        $country = (string)$order['shippingInfo']['postalAddress']['country'];


        //Buyer
        $shipToName = (string)$order['shippingInfo']['postalAddress']['name'];
        $phone = (string)$order['shippingInfo']['phone'];
        list($lastName, $firstName) = BuyerController::splitName($shipToName);
        $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);

        $Order = new Order(1, $channelName, WalmartClient::getStoreID(), $buyer, $orderNum, $purchaseDate,
            $shippingCode, $shippingPrice, $tax);

        //Save Orders
        if (!LOCAL) {
            $Order->save(WalmartClient::getStoreID());
        }
        $infoArray = $this->get_wm_order_items($orderItems, $state, $tax, $Order);
        $itemXml = $infoArray['item_xml'];
        $orderXml = $this->save_wm_order_to_xml($order, $itemXml, $Order, $buyer);

        if (!LOCAL) {
            FTPController::saveXml($orderNum, $orderXml, $channelName);
        }
    }

    public function process_orders($order, $totalTax, $shippingTotal, $orderTotal)
    {
        if (array_key_exists('tax', $order['charges']['charge'])) {
            foreach ($order['charges'] as $t) {
                $totalTax += number_format($t['tax']['taxAmount']['amount'], 2, '.', '');
//                echo "Taxes: $total_tax <br>";
            }
        }

        foreach ($order['charges'] as $p) {
            $orderTotal += $p['chargeAmount']['amount'];
        }

        foreach ($order['charges'] as $s) {
            if (in_array('SHIPPING', $s)) {
                foreach ($s['charges'] as $sa) {
                    if ($sa['chargeType'] == 'SHIPPING') {
                        $shippingTotal += number_format($sa['chargeAmount']['amount'], 2, '.', '');
                    }
                }
            }
        }
        return [
            'total_tax' => $totalTax,
            'shipping_total' => $shippingTotal,
            'order_total' => $orderTotal
        ];
    }

    public function get_wm_order_items($order_items, $state_code, $total_tax, Order $Order)
    {
        $wminv = new WalmartInventory();
        $item_xml = '';
        $poNumber = 1;

        foreach ($order_items as $i) {
            $quantity = $i['orderLineQuantity']['amount'];
            $title = $i['item']['productName'];
            $price = 0;
            foreach ($i['charges'] as $p) {
                if ($p['chargeType'] == 'PRODUCT') {
                    $price += $p['chargeAmount']['amount'];
                }
            }
            $price = sprintf("%01.2f", number_format($price, 2, '.', '') / $quantity);
            echo "Item Total: $price";
            $sku = $i['item']['sku'];
            $item = $wminv->getItem($sku);
            $upc = $item['MPItemView']['upc'];
            $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $poNumber);
            if (!LOCAL) {
                $orderItem->save($Order);
            }
            $item_xml .= OrderItemXMLController::create($orderItem);
            $poNumber++;
        }
        $item_xml .= TaxXMLController::getItemXml($state_code, $poNumber, $total_tax);
        $info_array = [
            'item_xml' => $item_xml
        ];
        return $info_array;
    }

    public function save_wm_order_to_xml($order, $itemXML, Order $Order)
    {
        $sku = $order['orderLines']['orderLine']['item']['sku'];
        $channelNumber = Channel::getAccountNumbersBySku($Order->getChannelName(), $sku);
        $xml = OrderXMLController::compile($channelNumber, $Order, $itemXML);
        return $xml;
    }
}