<?php

namespace rev;

use controllers\channels\FTPController;
use ecommerce\Ecommerce;
use models\channels\address\Address;
use models\channels\address\State;
use models\channels\Buyer;
use models\channels\Channel;
use models\channels\FTP;
use models\channels\Order;
use models\channels\OrderItem;
use models\channels\OrderItemXML;
use models\channels\OrderXML;
use models\channels\SKU;
use models\channels\Tax;

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

    public function saveOrders($request, Ecommerce $ecommerce, $folder)
    {
        $orders = substr($request, strpos($request, '"orders":'), -1);
        $orders = '{' . $orders . '}';
        $orders = json_decode($orders);

        if (!empty($orders)) {
            foreach ($orders as $o) {
                foreach ($o as $order) {
                    $order_num = $order->order_number;
                    $found = Order::get($order_num);
                    if (!$found) {
                        $ship_to_name = $order->buyer_name;
                        $name = explode(' ', $ship_to_name);
                        $last_name = ucwords(strtolower(array_pop($name)));
                        $first_name = ucwords(strtolower(implode(' ', $name)));
                        $state = $order->shipping_address->region;
                        $zip = $order->shipping_address->postal_code;
                        $city = $order->shipping_address->locality;
                        $address = $order->shipping_address->street_address;
                        $address2 = $order->shipping_address->extended_address;
                        $buyer_phone = $order->shipping_address->phone;
                        $country = $order->shipping_address->country_code;
                        if ($country == 'US') {
                            $country = 'USA';
                        }
                        $timestamp = $order->created_at;
                        $order_date = $timestamp;
                        $shipping = 'ZSTD';
                        $sku = $order->sku;
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
                        $item_xml = OrderItemXML::create($sku, $title, $ponumber, $quantity, $principle, $upc);
                        $ponumber++;
                        $item_xml .= Tax::getItemXml($state, $ponumber, $tax);
                        $total = number_format($principle / $quantity, 2);
                        if ($total >= 250) {
                            $shipping = 'URIP';
                        }
                        $state_id = State::getIdByAbbr($state);
                        $zip_id = Address::searchOrInsertZip($zip, $state_id);
                        $city_id = Address::searchOrInsertCity($city, $state_id);
                        $cust_id = Buyer::customer_soi($first_name, $last_name, ucwords(strtolower($address)),
                            ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                        if (!LOCAL) {
                            $order_id = Order::save(ReverbClient::getStoreID(), $cust_id, $order_num,
                                $shipping, $shipping_amount, $tax);
                        }
                        $sku_id = SKU::searchOrInsert($sku);
                        if (!LOCAL) {
                            OrderItem::save($order_id, $sku_id, $total, $quantity);
                        }
                        $xml = OrderXML::create($channel_num, $channelName, $order_num, $timestamp,
                            $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2,
                            $city, $state, $zip, $country, $item_xml);
                        if (!LOCAL) {
                            FTP::saveXml($order_num, $xml, $folder, $channelName);
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