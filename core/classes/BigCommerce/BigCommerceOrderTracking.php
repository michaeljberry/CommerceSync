<?php

namespace BigCommerce;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;
use ecommerce\Ecommerce;

class BigCommerceOrderTracking extends ChannelOrderTracking
{

    public function updateTracking(ChannelTracking $bigCommerceTracking, ChannelOrderTracking $bigCommerceOrderTracking)
    {
        $orderNumber = $bigCommerceOrderTracking->getOrderNumber();
        $shipment = BigCommerceOrder::getShippingInfo($orderNumber);
        $shipmentID = (string)$shipment[0]->id;
        $products = BigCommerceOrder::getOrderProducts($orderNumber);
        $filter = array(
            'order_address_id' => $shipmentID,
            'tracking_number' => $bigCommerceOrderTracking->getTrackingNumber(),
            'shipping_method' => $bigCommerceOrderTracking->getCarrier()
        );
        $items = [];
        foreach ($products as $product) {
            $items[] = array(
                'order_product_id' => (string)$product->id,
                'quantity' => (string)$product->quantity
            );
        }
        $filter['items'] = $items;
        $add_tracking = $this->postTrackingInfo($orderNumber, $filter);
        return $add_tracking;
    }

    public function postTrackingInfo($orderNumber, $filter)
    {
        $post_string = json_encode($filter);
        $apiURL = 'https://mymusiclife.com/api/v2/orders/' . $orderNumber . '/shipments';
//        $response = BigCommerceClient::bigcommerceCurl($apiURL, 'POST', $post_string);

        $order = json_decode($response);
        return $order;
    }

    public function updated($response)
    {
        Ecommerce::dd($response);
        if ($response) {
            return true;
        }
        return false;
    }
}