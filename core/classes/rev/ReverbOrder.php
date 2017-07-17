<?php

namespace rev;

use controllers\channels\BuyerController;
use controllers\channels\FTPController;
use ecommerce\Ecommerce;
use models\channels\address\Address;
use models\channels\address\City;
use models\channels\address\State;
use models\channels\address\ZipCode;
use models\channels\Buyer;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\order\OrderItemXMLController;
use controllers\channels\order\OrderXMLController;
use models\channels\SKU;
use models\channels\Tax;
use controllers\channels\tax\TaxXMLController;

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

    public function getOrders()
    {
        $url = 'https://api.reverb.com/api/my/orders/selling/awaiting_shipment/';
        $response = ReverbClient::reverbCurl($url, 'GET');
        return $response;
    }

    public function getOrder($order)
    {
        $url = 'https://reverb.com/api/my/orders/selling/' . $order;
        $response = ReverbClient::reverbCurl($url, 'GET');
        return $response;
    }

    public function saveOrders($request)
    {
        $orders = substr($request, strpos($request, '"orders":'), -1);
        $orders = '{' . $orders . '}';
        $orders = json_decode($orders);

        if (!empty($orders)) {
            foreach ($orders as $o) {
                foreach ($o as $order) {
                    $order_num = $order->order_number;
                    $found = Order::get($order_num);
                    if (LOCAL || !$found) {

                        $timestamp = $order->created_at;


                        //Address
                        $streetAddress = (string)$order->shipping_address->street_address;
                        $streetAddress2 = (string)$order->shipping_address->extended_address;
                        $city = $order->shipping_address->locality;
                        $state = $order->shipping_address->region;
                        $zipCode = $order->shipping_address->postal_code;
                        $country = $order->shipping_address->country_code;


                        //Buyer
                        $shipToName = (string)$order->buyer_name;
                        $buyer_phone = $order->shipping_address->phone;
                        list($lastName, $firstName) = BuyerController::splitName($shipToName);
                        $custID = (new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state,
                            $zipCode, $country))->getBuyerId();


                        //Order Items
                        $shipping = 'ZSTD';
                        $sku = (string)$order->sku;
                        $sku = ReverbOrder::clean_sku($sku);
                        $title = $order->title;
                        $quantity = $order->quantity;
                        $upc = '';
                        $principle = $order->amount_product_subtotal->amount;
                        $principle = number_format($principle / $quantity, 2, '.', '');
                        $shipping_amount = $order->shipping->amount;
                        $ponumber = 1;
                        $channelName = 'Reverb';
                        $channel_num = Channel::getAccountNumbersBySku($channelName, $sku);
                        $tax = 0;
                        if (strcasecmp($state, 'ID') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $principle * .06;
                            $principle -= $tax;
                        } elseif (strcasecmp($state, 'CA') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $principle * .09;
                            $principle -= $tax;
                        } elseif (strcasecmp($state, 'WA') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $principle * .09;
                            $principle -= $tax;
                        }
                        $item_xml = OrderItemXMLController::create($sku, $title, $ponumber, $quantity, $principle, $upc);
                        $ponumber++;
                        $item_xml .= TaxXMLController::getItemXml($state, $ponumber, $tax);
                        $total = number_format($principle / $quantity, 2);
                        if ($total >= 250) {
                            $shipping = 'URIP';
                        }


                        //Save Order
                        if (!LOCAL) {
                            $order_id = Order::save(ReverbClient::getStoreID(), $custID, $order_num,
                                $shipping, $shipping_amount, $tax);
                        }

                        $sku_id = SKU::searchOrInsert($sku);
                        if (!LOCAL) {
                            OrderItem::save($order_id, $sku_id, $total, $quantity);
                        }
                        $xml = OrderXMLController::create($channel_num, $channelName, $order_num, $timestamp, $shipping_amount, $shipping, $buyer_phone, $shipToName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country, $item_xml);
                        if (!LOCAL) {
                            FTPController::saveXml($order_num, $xml, $channelName);
                        }
                    } else {
                        echo 'Order ' . $order_num . ' is already in the database.<br>';
                    }
                }
            }
        }
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
}