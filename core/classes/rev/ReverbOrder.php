<?php

namespace rev;

use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;

class ReverbOrder extends Reverb
{
    public static function clean_sku($sku)
    {
        if (strpos($sku, ';') > 0) {
            $sku = substr($sku, 0, strpos($sku, ';'));
        } else {
            if (strpos($sku, ',') > 0) {
                $sku = substr($sku, 0, strpos($sku, ','));
            }
        }
        return $sku;
    }

    public function update_reverb_tracking($order_num, $tracking_id, $carrier, $notification = true)
    {
        $url = 'https://reverb.com/api/my/orders/selling/' . $order_num . '/ship';
        $postString = [
            'id' => $order_num,
            'provider' => $carrier,
            'tracking_number' => $tracking_id,
            'send_notification' => $notification,
        ];
        $response = ReverbClient::reverbCurl(
            $url,
            'POST',
            json_encode($postString)
        );
        return $response;
    }

    public static function getOrders()
    {
        $url = 'https://api.reverb.com/api/my/orders/selling/all?created_start_date=2017-07-18T12:00-00:00&created_end_date=2017-07-19T12:00-00:00'; //awaiting_shipment
        return ReverbClient::reverbCurl($url, 'GET');
    }

    public function getOrder($order)
    {
        $url = 'https://reverb.com/api/my/orders/selling/' . $order;
        $response = ReverbClient::reverbCurl($url, 'GET');
        return $response;
    }

    public function parseOrders($orders)
    {
        $jsonOrders = substr($orders, strpos($orders, '"orders":'), -1);
        $jsonOrders = '{' . $jsonOrders . '}';
        $jsonOrders = json_decode($jsonOrders);

        foreach ($jsonOrders->orders as $order) {
            $this->parseOrder($order);
        }
    }

    protected function parseOrder($order)
    {
        $orderNum = $order->order_number;
        $found = Order::get($orderNum);
        if (LOCAL || !$found) {
            $this->orderFound($order, $orderNum);
        }
    }

    protected function orderFound($order, $orderNum)
    {
        Ecommerce::dd($order);
        $channelName = 'Reverb';

        $purchaseDate = (string)$order->created_at;

        $total = (float)$order->total->amount;

        //Address
        $streetAddress = (string)$order->shipping_address->street_address;
        $streetAddress2 = (string)$order->shipping_address->extended_address;
        $city = (string)$order->shipping_address->locality;
        $state = (string)$order->shipping_address->region;
        $zipCode = (string)$order->shipping_address->postal_code;
        $country = (string)$order->shipping_address->country_code;

        $shippingCode = Order::shippingCode($total);
        $shippingPrice = (float)$order->shipping->amount;

        $sellingFee = (float)$order->selling_fee->amount;
        $directCheckoutFee = (float)$order->direct_checkout_fee->amount;
        $fee = $sellingFee + $directCheckoutFee;
        $tax = (float)$order->amount_tax->amount;

        //Buyer
        $lastName = (string)$order->buyer_last_name;
        $firstName = (string)$order->buyer_first_name;
        $phone = (string)$order->shipping_address->phone;
        $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);

        $Order = new Order(1, $channelName, ReverbClient::getStoreID(), $buyer, $orderNum,
            $purchaseDate, $shippingCode, $shippingPrice, $tax, $fee);

        //Save Order
        if (!LOCAL) {
            $Order->save(ReverbClient::getStoreID());
        }

        $this->getItems($Order, $order);

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL) {
            FTPController::saveXml($Order);
        }
    }

    protected function getItems(Order $Order, $item)
    {
        $this->parseItems($Order, $item);
    }

    protected function parseItems(Order $Order, $item)
    {
        $sku = '';
        if(isset($item->sku)) {
            $sku = (string)$item->sku;
            $sku = ReverbOrder::clean_sku($sku);
        }
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $title = (string)$item->title;
        $quantity = (int)$item->quantity;
        $upc = '';

        $price = (float)$item->amount_product_subtotal->amount;

        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber());
        $Order->setOrderItems($orderItem);
        if (!LOCAL) {
            $orderItem->save($Order);
        }
    }
}