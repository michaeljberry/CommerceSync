<?php

namespace rev;

use ecommerce\Ecommerce;
use models\channels\Address;
use models\channels\Order;
use models\channels\SKU;

class ReverbOrder extends Reverb
{
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
                    $found = Ecommerce::orderExists($order_num);
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
                        $sku = $ecommerce->clean_sku($sku);
                        $title = $order->title;
                        $quantity = $order->quantity;
                        $upc = '';
                        $principle = $order->amount_product_subtotal->amount;
                        $principle = number_format($principle / $quantity, 2, '.', '');
                        $shipping_amount = $order->shipping->amount;
                        $ponumber = 1;
                        $channelName = 'Reverb';
                        $channel_num = $ecommerce->get_channel_num($channelName, $sku);
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
                        $item_xml = $ecommerce->create_item_xml($sku, $title, $ponumber, $quantity, $principle, $upc);
                        $ponumber++;
                        $item_xml .= $ecommerce->get_tax_item_xml($state, $ponumber, $tax);
                        $total = number_format($principle / $quantity, 2);
                        if ($total >= 250) {
                            $shipping = 'URIP';
                        }
                        $state_id = Address::stateId($state);
                        $zip_id = Address::zipSoi($zip, $state_id);
                        $city_id = Address::citySoi($city, $state_id);
                        $cust_id = $ecommerce->customer_soi($first_name, $last_name, ucwords(strtolower($address)), ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                        if (!LOCAL) {
                            $order_id = Order::save(ReverbClient::getStoreID(), $cust_id, $order_num,
                                $shipping, $shipping_amount, $tax);
                        }
                        $sku_id = SKU::searchOrInsert($sku);
                        if (!LOCAL) {
                            $ecommerce->save_order_items($order_id, $sku_id, $total, $quantity);
                        }
                        $xml = $ecommerce->create_xml($channel_num, $channelName, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
                        if (!LOCAL) {
                            $ecommerce->saveXmlToFTP($order_num, $xml, $folder, $channelName);
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