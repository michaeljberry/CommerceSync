<?php

namespace rev;

use rev\reverbclass;
use ecommerceclass\ecommerceclass as ecom;

class revordclass extends reverbclass
{
    public function get_orders($order = null){
        if(empty($order)) {
            $url = 'https://api.reverb.com/api/my/orders/selling/awaiting_shipment/';
        }else{
            $url = 'https://reverb.com/api/my/orders/selling/' . $order;
        }
        $post_string = '';
        $response = $this->reverbCurl($url, 'GET', $post_string);
        return $response;
    }

    public function save_orders($request, $ecommerce, $ibmdata){
        $orders = substr($request, strpos($request, '"orders":'), -1);
        $orders = '{' . $orders . '}';
        $orders = json_decode($orders);

        if(!empty($orders)) {
            foreach ($orders as $o) {
                foreach ($o as $order) {
                    $order_num = $order->order_number;
                    $found = $ecommerce->orderExists($order_num);
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
                        $channel_name = 'Reverb';
                        $channel_num = $ecommerce->get_channel_num($ibmdata, $channel_name, $sku);
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
                        $state_id = $ecommerce->stateId($state);
                        $zip_id = $ecommerce->zipSoi($zip, $state_id);
                        $city_id = $ecommerce->citySoi($city, $state_id);
                        $cust_id = $ecommerce->customer_soi($first_name, $last_name, ucwords(strtolower($address)), ucwords(strtolower($address2)), $city_id, $state_id, $zip_id);
                        $order_id = $ecommerce->save_order($this->reverb_store_id, $cust_id, $order_num, $shipping, $shipping_amount, $tax);
                        $sku_id = $ecommerce->skuSoi($sku);
                        $ecommerce->save_order_items($order_id, $sku_id, $total, $quantity);
                        $xml = $ecommerce->create_xml($channel_num, $channel_name, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
                        $ecommerce->save_xml_to_hd($order_num, $xml, 'Reverb');
                    } else {
                        echo 'Order ' . $order_num . ' is already in the database.<br>';
                    }
                }
            }
        }
    }
    public function update_reverb_tracking($order_num, $tracking_id, $carrier, $notification = true){
        $url = 'https://reverb.com/api/my/orders/selling/' . $order_num . '/ship';
        $postString = [
            'id' => $order_num,
            'provider' => $carrier,
            'tracking_number' => $tracking_id,
            'send_notification' => $notification,
        ];
        $response = $this->reverbCurl(
            $url,
            'POST',
            json_encode($postString)
        );
        return $response;
    }
}