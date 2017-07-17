<?php

namespace bc;

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

class BigCommerceOrder extends BigCommerce
{
    public function get_bc_orders($BC, $filter)
    {
        $orders = $BC::getOrders($filter);
        if ($orders) {
            foreach ($orders as $o) {
                $orderNum = $o->id;
                $found = Order::get($orderNum);
                if (LOCAL || !$found) {
                    $state_code = $o->shipping_addresses[0]->state;
                    $tax = $o->total_tax;
                    $ship_info = $this->get_bc_order_ship_info($orderNum);
                    $shippingCode = 'ZSTD';
                    $total_order = $o->total_ex_tax;
                    if ($total_order > 299) {
                        $shippingCode = 'URIP';
                    }


                    $shippingPrice = number_format($o->shipping_cost_inc_tax, 2);


                    //Address
                    $streetAddress = (string)$ship_info[0]->street_1;
                    $streetAddress2 = (string)$ship_info[0]->street_2;
                    $city = (string)$ship_info[0]->city;
                    $state = (string)$ship_info[0]->state;
                    $zipCode = (string)$ship_info[0]->zip;
                    $country = (string)$ship_info[0]->country;


                    //Buyer
                    $firstName = (string)$ship_info[0]->first_name;
                    $lastName = (string)$ship_info[0]->last_name;
                    $buyerPhone = (string)$ship_info[0]->phone;
                    $buyer = new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country);

                    $Order = new Order(BigCommerceClient::getStoreID(), $buyer, $orderNum, $shippingCode, $shippingPrice, $tax);

                    //Save Orders
                    if (!LOCAL) {
//                        $order_id = Order::save(BigCommerceClient::getStoreID(), $buyerID, $orderNum, $shipping, $shipping_amount, $total_tax);
                        $Order->save(BigCommerceClient::getStoreID());
                    }
                    $response = $this->getOrderItems($orderNum);
                    $info_array = $this->parseItems($response, $state_code, $tax, $Order);
                    $item_xml = $info_array['item_xml'];
                    $channelName = 'BigCommerce';
                    $xml = $this->save_bc_order_to_xml($o, $item_xml, $firstName, $lastName, $shippingCode, $buyerPhone, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country);
                    if (!LOCAL) {
                        FTPController::saveXml($orderNum, $xml, $channelName);
                    }
                } else {
                    echo 'Order ' . $orderNum . ' is already in the database.';
                }
            }
        }
    }

    public function test_get_bc_orders($BC, $filter = '')
    {
        $orders = $BC::getOrders($filter);
        print_r($orders);
        if ($orders) {
            foreach ($orders as $o) {
                echo 'Order ID: ' . $o->id . "<br>";
                echo 'Customer ID: ' . $o->customer_id . "<br>";
                echo 'Date of order: ' . $o->date_created . "<br>";
                echo 'Date Last Modified: ' . $o->date_modified . "<br>";
                echo 'Date Shipped: ' . $o->date_shipped . "<br>";
                echo 'Status: ' . $o->status . "<br>";
                echo 'Subtotal w/o Tax: ' . $o->subtotal_ex_tax . "<br>";
                echo 'Subtotal w Tax: ' . $o->subtotal_inc_tax . "<br>";
                echo 'Tax Subtotal: ' . $o->subtotal_tax . "<br>";
                echo 'Base Shipping Cost: ' . $o->base_shipping_cost . "<br>";
                echo 'Shipping Cost w/o Tax: ' . $o->shipping_cost_ex_tax . "<br>";
                echo 'Shipping Cost w Tax: ' . $o->shipping_cost_inc_tax . " OR " . number_format($o->shipping_cost_inc_tax, 2) . "<br>";
                echo 'Tax for Shipping: ' . $o->shipping_cost_tax . "<br>";
                echo 'Base Handling Cost: ' . $o->base_handling_cost . "<br>";
                echo 'Handling Cost w/o tax: ' . $o->handling_cost_ex_tax . "<br>";
                echo 'Handling Cost w/ tax: ' . $o->handling_cost_inc_tax . "<br>";
                echo 'Tax for Handling: ' . $o->handling_cost_tax . "<br>";
                echo 'Base Wrapping Cost: ' . $o->base_wrapping_cost . "<br>";
                echo 'Wrapping Cost w/o tax: ' . $o->wrapping_cost_ex_tax . "<br>";
                echo 'Wrapping Cost w/ tax: ' . $o->wrapping_cost_inc_tax . "<br>";
                echo 'Tax for Wrapping: ' . $o->wrapping_cost_tax . "<br>";
                echo 'Total w/o Tax: ' . $o->total_ex_tax . "<br>";
                echo 'Total w/ Tax: ' . $o->total_inc_tax . "<br>";
                echo 'Tax Total: ' . $o->total_tax . "<br>";
                echo 'Items Total: ' . $o->items_total . "<br>";
                echo 'Items Shipped: ' . $o->items_shipped . "<br>";
                echo 'Payment Method: ' . $o->payment_method . "<br>";
                echo 'Payment Provider ID: ' . $o->payment_provider_id . "<br>";
                echo 'Payment Status: ' . $o->payment_status . "<br>";
                echo 'Refunded Amount: ' . $o->refunded_amount . "<br>";
                echo 'Is order digital: ' . $o->order_is_digital . "<br>";
                echo 'Store Credit Amount: ' . $o->store_credit_amount . "<br>";
                echo 'Gift Certificate Amount: ' . $o->gift_certificate_amount . "<br>";
                echo 'IP address: ' . $o->ip_address . "<br>";
                echo 'GEO IP Country: ' . $o->geoip_country . "<br>";
                echo 'GEO IP Country 2: ' . $o->geoip_country_iso2 . "<br>";
                echo 'Currency ID: ' . $o->currency_id . "<br>";
                echo 'Currency Code: ' . $o->currency_code . "<br>";
                echo 'Currency Exchange Rate: ' . $o->currency_exchange_rate . "<br>";
                echo 'Staff notes: ' . $o->staff_notes . "<br>";
                echo 'Customer note: ' . $o->customer_message . "<br>";
                echo 'Discount Amount: ' . $o->discount_amount . "<br>";
                echo 'Coupon Discount: ' . $o->coupon_discount . "<br>";
                echo 'Billing Address: ';
                print_r($o->billing_address);
                echo '<br>' . $o->billing_address->first_name;
                echo "<br>";
                echo 'Products: ';
                print_r($o->products);
                echo '<br>';
                echo 'Shipping Address: ';
                print_r($o->shipping_addresses);
                echo '<br>';
                echo 'Coupons: ';
                print_r($o->coupons);
                echo '<br>';
                $items = $this->get_bc_order_items($o->id);
                print_r($items);
                foreach ($items as $i) {
                    echo 'Order ID: ' . $i->order_id . "<br>";
                    echo 'Product ID: ' . $i->product_id . "<br>";
                    echo 'Product Name: ' . $i->name . "<br>";
                    echo 'SKU: ' . $i->sku . "<br>";
                    echo 'Base Price: ' . $i->base_price . "<br>";
                    echo 'Price w/o Tax: ' . $i->price_ex_tax . "<br>";
                    echo 'Price w/ Tax: ' . $i->price_inc_tax . "<br>";
                    echo 'Tax: ' . $i->price_tax . "<br>";
                    echo 'Base Total: ' . $i->base_total . "<br>";
                    echo 'Total w/o Tax: ' . $i->total_ex_tax . "<br>";
                    echo 'Total w/ Tax: ' . $i->total_inc_tax . "<br>";
                    echo 'Total Tax: ' . $i->total_tax . "<br>";
                    echo 'Weight: ' . $i->weight . "<br>";
                    echo 'Quantity: ' . $i->quantity . "<br>";
                    echo 'Base Cost: ' . $i->base_cost_price . "<br>";
                    echo 'Cost w/o Tax: ' . $i->cost_price_ex_tax . "<br>";
                    echo 'Cost w/ Tax: ' . $i->cost_price_inc_tax . "<br>";
                    echo 'Tax on Cost: ' . $i->cost_price_tax . "<br>";
                    echo 'Is refunded: ' . $i->is_refunded . "<br>";
                    echo 'Refund Amount: ' . $i->refund_amount . "<br>";
                    echo ': ' . $i->refund_amount . "<br>";
                }
            }
        } else {
            echo 'There were no orders in the three days.';
        }
    }

    public function get_bc_order_product($orderNum)
    {
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $orderNum . '/products.json';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET');

        $items = json_decode($response);
        return $items;
    }

    public function getOrderItems($orderNum)
    {
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $orderNum . '/products.json';
        return BigCommerceClient::bigcommerceCurl($api_url, 'GET');
    }

    public function parseItems($response, $state_code, $total_tax, $Order)
    {
        $items = json_decode($response);
        $item_xml = '';
        $poNumber = 1;
        foreach ($items as $i) {
            Ecommerce::dd($i);
            $product_id = (string)$i->product_id;
            $quantity = (integer)$i->quantity;
            $title = (string)$i->name;
            $principle = (float)$i->total_ex_tax;
            $price = Ecommerce::removeCommasInNumber($principle) / $quantity;
            $sku = (string)$i->sku;
            $upc = $this->get_bc_product_upc($product_id);
            $skuID = SKU::searchOrInsert($sku);
            $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $poNumber);
            if (!LOCAL) {
//                OrderItem::save($orderID, $skuID, $price, $quantity);
                $orderItem->save($Order);
            }
            $item_xml .= OrderItemXMLController::create($sku, $title, $poNumber, $quantity, $price, $upc);
            $poNumber++;
        }
        $item_xml .= TaxXMLController::getItemXml($state_code, $poNumber, $total_tax);
        $info_array = [
            'item_xml' => $item_xml
        ];
        return $info_array;
    }

    public function get_bc_order_ship_info($order_id)
    {
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $order_id . '/shippingaddresses.json';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET');

        $shipping = json_decode($response);
        return $shipping;
    }

    public function get_bc_order_info($order_id)
    {
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $order_id;
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'GET');

        $order = json_decode($response);
        return $order;
    }

    public function post_bc_tracking_info($order_id, $shipment_id, $filter)
    {
        $post_string = json_encode($filter);
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $order_id . '/shipments';
        $response = BigCommerceClient::bigcommerceCurl($api_url, 'POST', $post_string);
        Ecommerce::dd('Post BC Tracking Info');
        Ecommerce::dd($response);

        $order = json_decode($response);
        return $order;
    }

    public function save_bc_order_to_xml($o, $item_xml, $first_name, $last_name, $shipping, $buyer_phone, $address, $address2, $city, $state, $zip, $country)
    {
        $sku = Ecommerce::substring_between($item_xml, '<ItemId>', '</ItemId>');
        $channel_name = 'Store';
        $channel = "BigCommerce";
        $channel_num = Channel::getAccountNumbersBySku($channel, $sku);
        $orderNum = $o->id;
        $timestamp = $o->date_created;
        $timestamp = date("Y-m-d H:i:s", strtotime($timestamp));
        $timestamp = str_replace(' ', 'T', $timestamp);
        $timestamp = $timestamp . '.000Z';
        $shipping_amount = number_format($o->shipping_cost_inc_tax, 2);
        $order_date = $timestamp;
        $ship_to_name = $first_name . ' ' . $last_name;
        $xml = OrderXMLController::create($channel_num, $channel_name, $orderNum, $timestamp, $shipping_amount, $shipping, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
        return $xml;
    }

    public function update_bc_tracking($order_id, $tracking_num, $carrier)
    {
        $shipment = $this->get_bc_order_ship_info($order_id);
        $shipment_id = $shipment[0]->id;
        $products = $this->get_bc_order_product($order_id);
        $filter = array(
            'order_address_id' => $shipment_id,
            'tracking_number' => $tracking_num,
            'shipping_method' => $carrier
        );
        foreach ($products as $product) {
            $items[] = array(
                'order_product_id' => $product->id,
                'quantity' => $product->quantity
            );
        }
        $filter['items'] = $items;
        $add_tracking = $this->post_bc_tracking_info($order_id, $shipment_id, $filter);
        print_r($add_tracking);
        return $add_tracking;
    }

}