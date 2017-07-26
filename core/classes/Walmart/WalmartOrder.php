<?php

namespace Walmart;

use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;
use controllers\channels\BuyerController;
use WalmartAPI\Order as WMOrder;
use Exception;

class WalmartOrder extends Walmart
{

    private static $limit = 10;

    public static function configure(): WMOrder
    {
        return new WMOrder([
            'consumerId' => WalmartClient::getConsumerKey(),
            'privateKey' => WalmartClient::getSecretKey(),
            'wmConsumerChannelType' => WalmartClient::getAPIHeader()
        ]);
    }

    protected static function acknowledgeOrder($orderNum)
    {
        WalmartOrder::configure()->acknowledge([
            'purchaseOrderId' => $orderNum,
        ]);
    }

    protected static function isMulti($array): bool
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                return true;
            }
        }
        return false;
    }

    protected static function isAcknowledged($orderLine): bool
    {
        if (!WalmartOrder::isMulti($orderLine)) {
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
            $orders = WalmartOrder::configure()->listAll([
                'nextCursor' => $next
            ]);
            $this->parseOrders($orders);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    public static function getOrder($orderNum)
    {
        try {
            $order = WalmartOrder::configure()->get([
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
            $fromDate = '-' . Walmart::getApiOrderDays() . ' days';

            return WalmartOrder::configure()->listAll([
                'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                'limit' => WalmartOrder::$limit
            ]);
        } catch (Exception $e) {
            die("There was a problem requesting the data: " . $e->getMessage());
        }
    }

    public function parseOrders($orders)
    {
        if (WalmartOrder::isMulti($orders['elements']['order'])) {
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
            WalmartOrder::acknowledgeOrder($orderNum);
        }
        if (WalmartOrder::isAcknowledged($order['orderLines']['orderLine'])) {
            Ecommerce::dd($order);
            $channelName = 'Walmart';

            $purchaseDate = (string)$order['orderDate'];

            $tax = 0.00;
            $shippingPrice = 0.00;
            $orderTotal = 0.00;

            if (!WalmartOrder::isMulti($order['orderLines']['orderLine'])) {
                $orderTotal = $this->getOrderTotal($order['orderLines']['orderLine'], $orderTotal);

                $orderItems = $order['orderLines'];
            } else {
                foreach ($order['orderLines']['orderLine'] as $orderLine) {
                    $orderTotal += $this->getOrderTotal($orderLine, $orderTotal);
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

            $Order = new Order(1, $channelName, WalmartClient::getStoreId(), $buyer, $orderNum, $purchaseDate,
                $shippingCode, $shippingPrice, $tax);

            //Save Orders
            if (!LOCAL) {
                $Order->save(WalmartClient::getStoreId());
            }
            $Order->setOrderId();

            $this->getItems($Order, $orderItems);

            $tax = $Order->getTax()->get();

            Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

            $Order->setOrderXml($Order);

            if (!LOCAL) {
                FTPController::saveXml($Order);
            }
        }
    }

    protected function getItems(Order $Order, $orderItems)
    {
        $this->parseItems($Order, $orderItems);
    }

    protected function parseItems(Order $Order, $orderItems)
    {
        foreach ($orderItems as $item) {
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

        return $orderTotal;
    }
}
