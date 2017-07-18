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
                    $orderNum = $order->order_number;
                    $found = Order::get($orderNum);
                    if (LOCAL || !$found) {

                        Ecommerce::dd($order);
                        $channelName = 'Reverb';

                        $purchaseDate = (string)$order->created_at;

                        //Address
                        $streetAddress = (string)$order->shipping_address->street_address;
                        $streetAddress2 = (string)$order->shipping_address->extended_address;
                        $city = (string)$order->shipping_address->locality;
                        $state = (string)$order->shipping_address->region;
                        $zipCode = (string)$order->shipping_address->postal_code;
                        $country = (string)$order->shipping_address->country_code;


                        //Buyer
                        $shipToName = (string)$order->buyer_name;
                        $phone = (string)$order->shipping_address->phone;
                        list($lastName, $firstName) = BuyerController::splitName($shipToName);
                        $buyer = new Buyer($firstName, $lastName, $streetAddress, $streetAddress2, $city, $state, $zipCode, $country, $phone);

                        //Order Items
                        $shippingCode = 'ZSTD';
                        $sku = (string)$order->sku;
                        $sku = ReverbOrder::clean_sku($sku);
                        $title = (string)$order->title;
                        $quantity = (int)$order->quantity;
                        $upc = '';
                        $price = (float)$order->amount_product_subtotal->amount;
                        $price = number_format($price / $quantity, 2, '.', '');
                        $shippingPrice = (float)$order->shipping->amount;
                        $poNumber = 1;

                        $channelNumber = Channel::getAccountNumbersBySku($channelName, $sku);
                        $tax = 0;
                        if (strcasecmp($state, 'ID') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $price * .06;
                            $price -= $tax;
                        } elseif (strcasecmp($state, 'CA') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $price * .09;
                            $price -= $tax;
                        } elseif (strcasecmp($state, 'WA') == 0) {
                            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
                            $tax = $price * .09;
                            $price -= $tax;
                        }
                        $total = number_format($price / $quantity, 2);
                        if ($total >= 250) {
                            $shippingCode = 'URIP';
                        }

                        $orderItem = new OrderItem($sku, $title, $quantity, $price, $upc, $poNumber);
                        $itemXML = OrderItemXMLController::create($orderItem);
                        $poNumber++;
                        $itemXML .= TaxXMLController::getItemXml($state, $poNumber, $tax);


                        $Order = new Order(1, $channelName, ReverbClient::getStoreID(), $buyer, $orderNum,
                            $purchaseDate, $shippingCode, $shippingPrice, $tax);

                        //Save Order
                        if (!LOCAL) {
                            $Order->save(ReverbClient::getStoreID());
                        }

                        $sku_id = SKU::searchOrInsert($sku);

                        if (!LOCAL) {
//                            OrderItem::save($order_id, $sku_id, $total, $quantity);
                            $orderItem->save($Order);
                        }
                        $xml = OrderXMLController::create($channelNumber, $Order, $itemXML);
                        if (!LOCAL) {
                            FTPController::saveXml($orderNum, $xml, $channelName);
                        }
                    } else {
                        echo 'Order ' . $orderNum . ' is already in the database.<br>';
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