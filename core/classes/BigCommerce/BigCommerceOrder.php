<?php

namespace BigCommerce;

use Ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\order\OrderItem;
use controllers\channels\FTPController;
use Bigcommerce\Api\Client;

class BigCommerceOrder extends BigCommerce
{
    public function test_get_bc_orders(Client $BC, $filter = '')
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

    public static function getOrderProducts($orderNum)
    {
        $apiURL = 'https://mymusiclife.com/api/v2/orders/' . $orderNum . '/products.json';
        $response = BigCommerceClient::bigcommerceCurl($apiURL, 'GET');

        $items = json_decode($response);
        return $items;
    }

    public static function getShippingInfo($orderNumber)
    {
        $apiURL = 'https://mymusiclife.com/api/v2/orders/' . $orderNumber . '/shippingaddresses.json';
        $response = BigCommerceClient::bigcommerceCurl($apiURL, 'GET');

        $shipping = json_decode($response);
        return $shipping;
    }

    public static function getOrders(Client $BC)
    {
        $fromDate = "-" . BigCommerce::getApiOrderDays() . " days";
        $filter = array(
            'min_date_created' => date('r', strtotime($fromDate)),
            'status_id' => 11
        );

        return $BC::getOrders($filter);
    }

    public function parseOrders($orders)
    {
        foreach ($orders as $order) {
            $this->parseOrder($order);
        }
    }

    protected function parseOrder($order)
    {
        $orderNum = $order->id;
        $found = Order::get($orderNum);
        if (LOCAL || !$found) {

            $this->orderFound($order, $orderNum);
        }
    }

    protected function orderFound($order, $orderNum)
    {
        Ecommerce::dd($order);
        $channelName = 'BigCommerce';

        $purchaseDate = (string)$order->date_created;

        $total = $order->total_ex_tax;

        $shippingCode = Order::shippingCode($total);
        $shippingPrice = number_format($order->shipping_cost_inc_tax, 2);

        $tax = (float)$order->total_tax;

        //Address
        $ship_info = BigCommerceOrder::getShippingInfo($orderNum);
        $streetAddress = (string)$ship_info[0]->street_1;
        $streetAddress2 = (string)$ship_info[0]->street_2;
        $city = (string)$ship_info[0]->city;
        $state = (string)$ship_info[0]->state;
        $zipCode = (string)$ship_info[0]->zip;
        $country = (string)$ship_info[0]->country;


        //Buyer
        $firstName = (string)$ship_info[0]->first_name;
        $lastName = (string)$ship_info[0]->last_name;
        $phone = (string)$ship_info[0]->phone;
        $buyer = Order::buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode,
            $country, $phone);

        $Order = new Order(1, $channelName, BigCommerceClient::getStoreId(), $buyer, $orderNum,
            $purchaseDate, $shippingCode, $shippingPrice, $tax);

        //Save Orders
        if (!LOCAL) {
            $Order->save(BigCommerceClient::getStoreId());
        }
        $Order->setOrderId();

        $items = $this->getItems($orderNum);

        $this->parseItems($Order, $items);

        $tax = $Order->getTax()->get();

        Order::updateShippingAndTaxes($Order->getOrderId(), $Order->getShippingPrice(), $tax);

        $Order->setOrderXml($Order);

        if (!LOCAL) {
            FTPController::saveXml($Order);
        }
    }

    public function getItems($orderNum)
    {
        $api_url = 'https://mymusiclife.com/api/v2/orders/' . $orderNum . '/products.json';
        return BigCommerceClient::bigcommerceCurl($api_url, 'GET');
    }

    public function parseItems(Order $Order, $items)
    {
        $items = json_decode($items);
        foreach ($items as $item) {
            $this->parseItem($Order, $item);
        }
    }

    protected function parseItem(Order $Order, $item)
    {
        $sku = (string)$item->sku;
        $Order->setChannelAccount(Channel::getAccountNumbersBySku($Order->getChannelName(), $sku));

        $quantity = (integer)$item->quantity;
        $title = (string)$item->name;

        $productID = (string)$item->product_id;
        $product = $this->get_bc_product_info($productID);
        $upc = $product->upc;

        $principle = (float)$item->total_ex_tax;
        $price = Ecommerce::formatMoneyNoComma($principle) / $quantity;

        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $Order->getPoNumber());
        $Order->setOrderItems($orderItem);
        if (!LOCAL) {
            $orderItem->save($Order);
        }
    }
}
