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
        $this->configure()->acknowledge([
            'purchaseOrderId' => $orderNum,
        ]);
    }

    protected function isMulti($array): bool
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
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
            $this->parseOrders($orders);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    public function getOrder($orderNum)
    {
        try {
            $wmOrder = $this->configure();
            $order = $wmOrder->get([
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
            $wmOrder = $this->configure();
            $fromDate = '-' . Walmart::getApiOrderDays() . ' days';

            $orders = $wmOrder->listAll([
                'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                'limit' => WalmartOrder::$limit
            ]);
            $this->parseOrders($orders);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    protected function parseOrders($orders)
    {
        if ($this->isMulti($orders['elements']['order'])) {
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

        if (isset($orders['meta']['nextCursor'])) {
            $nextCursor = $orders['meta']['nextCursor'];
            $orders = $this->getMoreOrders($nextCursor);

            $this->parseOrders($orders);
        }
    }

    protected function parseOrder($order)
    {
        $orderNum = $order['purchaseOrderId'];

        $found = Order::get($orderNum);

        if (LOCAL || !$found) {
            $this->orderFound($order, $orderNum);
        }
    }

    protected function orderFound($order, $orderNum)
    {
        if (!LOCAL) {
            $this->acknowledgeOrder($orderNum);
        }
        if ($this->isAcknowledged($order['orderLines']['orderLine'])) {
            Ecommerce::dd($order);
            $channelName = 'Walmart';

            $purchaseDate = (string)$order['orderDate'];

            $tax = 0.00;
            $shippingPrice = 0.00;
            $orderTotal = 0.00;

            echo "<br><br>Order: $orderNum<br><pre>";
            print_r($order);
            echo '</pre><br><br>';

            if (!$this->isMulti($order['orderLines']['orderLine'])) {
                $orderInfo = $this->getOrderTotal($order['orderLines']['orderLine'], $orderTotal);
                $orderTotal += $orderInfo['order_total'];

                $orderItems = $order['orderLines'];
            } else {
                foreach ($order['orderLines']['orderLine'] as $o) {
                    $orderInfo = $this->getOrderTotal($o, $orderTotal);
                    $orderTotal += $orderInfo['order_total'];
                }
                $orderItems = $order['orderLines']['orderLine'];
            }

            $shippingCode = Order::shippingCode($orderTotal);


            //Address
            $shippingInfo = $order['shippingInfo'];
            $postalAddress = $shippingInfo['postalAddress'];
            $streetAddress = (string)$postalAddress['address1'];
            $streetAddress2 = (string)$postalAddress['address2'] ?? '';
            $city = (string)$postalAddress['city'];
            $state = (string)$postalAddress['state'];
            $zipCode = (string)$postalAddress['postalCode'];
            $country = (string)$postalAddress['country'];


            //Buyer
            $shipToName = (string)$postalAddress['name'];
            $phone = (string)$shippingInfo['phone'];
            list($lastName, $firstName) = BuyerController::splitName($shipToName);
            $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
                $country, $phone);

            $Order = new Order(1, $channelName, WalmartClient::getStoreID(), $buyer, $orderNum, $purchaseDate,
                $shippingCode, $shippingPrice, $tax);

            //Save Orders
            if (!LOCAL) {
                $Order->save(WalmartClient::getStoreID());
            }

            $this->getItems($Order, $orderItems);

            $tax = $Order->getTax()->get();

            Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

            $Order->setOrderXml($Order);

            if (!LOCAL) {
                FTPController::saveXml($Order);
            }
        }
    }

    protected function getItems(Order $Order, $order_items)
    {
        $this->parseItems($Order, $order_items);
    }

    protected function parseItems(Order $Order, $order_items)
    {
        foreach ($order_items as $item) {
            $this->parseItem($Order, $item);
        }
    }

    protected function parseItem(Order $Order, $item)
    {
        $sku = $item['item']['sku'];
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $title = $item['item']['productName'];
        $quantity = $item['orderLineQuantity']['amount'];
        $upc = '';

        $tax = 0.00;
        $shipping = 0.00;
        $price = 0.00;
        foreach ($item['charges'] as $itemPrice) {
            if ($itemPrice['chargeType'] == 'PRODUCT') {
                $price += $itemPrice['chargeAmount']['amount'];
            }
            if ($itemPrice['chargeType'] == 'SHIPPING') {
                $shipping += $itemPrice['chargeAmount']['amount'];
            }
            if (array_key_exists('tax', $itemPrice)) {
                $tax += $itemPrice['tax']['taxAmount']['amount'];
            }
        }
        $price = sprintf("%01.2f", number_format($price, 2, '.', '') / $quantity);


        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber());
        $Order->setOrderItems($orderItem);
        if (!LOCAL) {
            $orderItem->save($Order);
        }
    }

    protected function getOrderTotal($order, $orderTotal)
    {
        foreach ($order['charges'] as $price) {
            $orderTotal += $price['chargeAmount']['amount'];
        }

        return [
            'order_total' => $orderTotal
        ];
    }
}