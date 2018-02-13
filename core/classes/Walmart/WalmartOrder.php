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

class WalmartOrder extends WalmartClient
{

    private static $limit = 10;

    public static function configure(): WMOrder
    {

        return new WMOrder(
            [
                'consumerId' => WalmartClient::getConsumerKey(),
                'privateKey' => WalmartClient::getSecretKey(),
                'wmConsumerChannelType' => WalmartClient::getAPIHeader()
            ]
        );

    }

    protected static function getOrderLimit()
    {

        return static::$limit;

    }

    protected static function acknowledgeOrder($orderNum)
    {

        static::configure()->acknowledge(
            [
                'purchaseOrderId' => $orderNum,
            ]
        );

    }

    protected static function hasMultipleOrders($array): bool
    {

        foreach ($array as $key => $value) {

            if (is_numeric($key)){
                return true;
            }

        }

        return false;

    }

    protected static function checkOrderAcknowledgeStatus($orderLine): bool
    {

        return (array_key_exists('orderLineStatuses',$orderLine) &&
                $orderLine['orderLineStatuses']['orderLineStatus']['status']
                == 'Acknowledged') ? true : false;

    }

    protected static function isAcknowledged($orderLine): bool
    {

        if (!static::hasMultipleOrders($orderLine)) {

            return static::checkOrderAcknowledgeStatus($orderLine);

        }

        return static::checkOrderAcknowledgeStatus($orderLine[0]);

    }

    public static function getMoreOrders($next)
    {

        try {

            $orders = static::configure()->listAll(
                [
                    'nextCursor' => $next
                ]
            );
            static::parseOrders($orders);

        } catch (Exception $e) {

            Ecommerce::dd("There was a problem requesting the data: " . $e->getMessage());

        }

    }

    public static function getOrder($orderNum)
    {

        try {

            $order = static::configure()->get(
                [
                    'purchaseOrderId' => $orderNum
                ]
            );
            return $order;

        } catch (Exception $e) {

            Ecommerce::dd("There was a problem requesting the data: " . $e->getMessage());

        }

    }

    public static function getOrders()
    {

        try {

            $fromDate = '-' . Walmart::getApiOrderDays() . ' days';

            return static::configure()->listAll(
                [
                    'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                    'limit' => static::$limit
                ]
            );

        } catch (Exception $e) {

            Ecommerce::dd("There was a problem requesting the data: " . $e->getMessage());

        }

    }

    protected static function parseEachOrder($orders)
    {

        foreach ($orders as $order) {

            static::parseOrder($order);

        }

    }

    public static function parseOrders($orders)
    {

        $multipleOrdersInCall = static::hasMultipleOrders($orders['elements']['order']);

        if ($multipleOrdersInCall) {

            echo "Multiple Orders<br>";
            static::parseEachOrder($orders['elements']['order']);

        } else {

            echo "Single Order:<br>";
            static::parseEachOrder($orders['elements']);

        }

        if (isset($orders['meta']['nextCursor'])) {

            $nextCursor = $orders['meta']['nextCursor'];
            $orders = static::getMoreOrders($nextCursor);

            static::parseOrders($orders);

        }

    }

    protected static function parseOrder($order)
    {

        $orderNum = $order['purchaseOrderId'];

        $found = Order::get($orderNum);

        if (LOCAL || !$found) {

            static::orderFound($order, $orderNum);

        }

    }

    protected static function orderFound($order, $orderNum)
    {

        if (!LOCAL) {

            static::acknowledgeOrder($orderNum);

        }

        $orderIsAcknowledged = static::isAcknowledged($order['orderLines']['orderLine']);

        if ($orderIsAcknowledged) {

            Ecommerce::dd($order);
            $channelName = 'Walmart';

            $purchaseDate = (string)$order['orderDate'];

            $tax = 0.00;
            $shippingPrice = 0.00;
            $orderTotal = 0.00;

            $multipleOrdersInCall = static::hasMultipleOrders($order['orderLines']['orderLine']);

            if (!$multipleOrdersInCall) {

                $orderTotal = static::getOrderTotal($order['orderLines']['orderLine'], $orderTotal);

                $orderItems = $order['orderLines'];

            } else {

                foreach ($order['orderLines']['orderLine'] as $orderLine) {

                    $orderTotal += static::getOrderTotal($orderLine, $orderTotal);

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

            static::getItems($Order, $orderItems);

            $tax = $Order->getTax()->get();

            Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

            $Order->setOrderXml($Order);

            if (!LOCAL) {

                FTPController::saveXml($Order);

            }

        }

    }

    protected static function getItems(Order $Order, $orderItems)
    {

        static::parseItems($Order, $orderItems);

    }

    protected static function parseItems(Order $Order, $orderItems)
    {

        foreach ($orderItems as $item) {

            static::parseItem($Order, $item);

        }

    }

    protected static function parseItem(Order $Order, $item)
    {

        $sku = $item['item']['sku'];
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $title = $item['item']['productName'];
        $quantity = $item['orderLineQuantity']['amount'];
        $upc = '';

        $tax = 0.00;
        $shipping = 0.00;
        $price = 0.00;
        list($price, $shipping, $tax) = static::calculateItemTotals($item['charges'], $price, $shipping, $tax);

        $price = sprintf("%01.2f", Ecommerce::formatMoneyNoComma($price) / $quantity);
        $totalTax = Ecommerce::formatMoney($tax);
        $Order->getTax()->updateTax($totalTax);


        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber());
        $Order->setOrderItems($orderItem);

        if (!LOCAL) {

            $orderItem->save($Order);

        }

    }

    protected static function getOrderTotal($order, $orderTotal)
    {

        foreach ($order['charges'] as $price) {

            $orderTotal += $price['chargeAmount']['amount'];

        }

        return $orderTotal;

    }

    protected static function calculateItemTotals($charges, $price, $shipping, $tax)
    {

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

        return [$price, $shipping, $tax];

    }
}
