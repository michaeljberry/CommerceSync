<?php

namespace controllers\channels;

use controllers\channels\order\ChannelOrderTracking;
use ecommerce\Ecommerce;
use IBM;
use models\channels\order\Order;
use models\channels\Tracking;

class TrackingController
{

    private static $folder = ROOTFOLDER;

    private function getFolder()
    {
        return TrackingController::$folder;
    }

    private static function getLogFileName()
    {
        return "Tracking - " . date('ymd') . ".txt";
    }

    public function getTrackingLog()
    {
        return TrackingController::getFolder() . 'log/tracking/' . TrackingController::getLogFileName();
    }

    public static function updateTracking($method = "auto")
    {
        $tracker = new Tracking();
        $orders = TrackingController::getUnshippedOrders();
        TrackingController::parseOrders($tracker, $orders, $method);
    }

    public static function getUnshippedOrders()
    {
        return Tracking::findUnshippedOrders();
    }

    protected static function parseOrders(Tracking $tracker, $orders, $method)
    {
        foreach ($orders as $order) {
            TrackingController::parseOrder($tracker, $order, $method);
        }
        if(is_object($tracker->getChannel('Amazon'))) {
            Ecommerce::dd('Update Amazon Tracking');
            $tracker->getChannel('Amazon')->updateAmazonTracking($tracker->getChannel('Amazon'));
        }
        Ecommerce::dd($tracker);
        TrackingController::markOrdersAsShipped($tracker);
    }

    protected static function parseOrder(Tracking $tracker, $order, $method)
    {
        $orderNumber = $order['order_num'];
        $orderID = Order::getIdByOrder($orderNumber);
        $channelName = $order['type'];

//        if($channelName == 'Walmart') {
        $tracker->setChannel($channelName);

        TrackingController::setTrackingNumbers($tracker, $channelName, $orderNumber, $orderID, $method);

        echo "$channelName: $orderNumber -> {$tracker->getTrackingNumber($channelName, $orderNumber)}<br>";
        TrackingController::setEbayId($tracker, $order, $channelName, $orderNumber);

        TrackingController::shipped($tracker, $channelName, $orderNumber, $orderID);
    }

    protected static function setTrackingNumbers(Tracking $tracker, $channelName, $orderNumber, $orderID, $method)
    {
        $trackingNumber = '';
        $carrier = '';

        if($method !== 'auto'){
            $carrier = 'USPS';
            $trackingNumber = trim(IBM::getManualTrackingNum($orderNumber, $tracker->getChannelNumbers($channelName)));
            if (empty($trackingNumber)) {
                $trackingNumber = trim(IBM::getSimilarTrackingNum($orderNumber, $tracker->getChannelNumbers($channelName)));
            }
            $tracker->setOrder($channelName, $orderNumber, $orderID, $trackingNumber, $carrier);
            return;
        }

        $trackingInfo = IBM::getTrackingNum($orderNumber, $tracker->getChannelNumbers($channelName));
        if (isset($trackingInfo['USPS'])) {
            $trackingNumber = trim($trackingInfo['USPS']);
            $carrier = 'USPS';
        } elseif (isset($trackingInfo['UPS'])) {
            $trackingNumber = trim($trackingNumber['UPS']);
            $carrier = 'UPS';
        }
        $tracker->setOrder($channelName, $orderNumber, $orderID, $trackingNumber, $carrier);

        return;
    }

    protected static function setEbayId(Tracking $tracker, $order, $channelName, $orderNumber)
    {
        if ($channelName == 'Ebay') {
            $itemID = $order['item_id'];
            $transactionID = '';
            if (!empty($itemID)) {
                echo "Item ID: $itemID<br>";
                $numID = explode('-', $itemID);
                $itemID = $numID[0];
                $transactionID = $numID[1];
            }

            $tracker->setItemTransactionId($channelName, $orderNumber, $itemID, $transactionID);
        }
    }

    protected static function shipped(Tracking $tracker, $channelName, $orderNumber, $orderID)
    {
        if (!empty($tracker->getTrackingNumber($channelName, $orderNumber))) {
            $trackingNumber = $tracker->getOrder($channelName, $orderNumber)->getTrackingNumber();
            $carrier = $tracker->getOrder($channelName, $orderNumber)->getCarrier();
            echo $orderID . ': ' . $trackingNumber . '; Channel: ' . $channelName . '<br>';

            Tracking::updateTrackingNumber(
                $tracker->getOrder($channelName, $orderNumber)->getOrderId(),
                $trackingNumber,
                $carrier
            );

            $channelOrderTracking = $tracker->getOrder($channelName, $orderNumber);
            $response = $channelOrderTracking->updateTracking($tracker->getChannel($channelName),
                $channelOrderTracking);

            Ecommerce::dd($response);
            $updated = $channelOrderTracking->updated($response);

            TrackingController::setShipped($channelName, $updated, $channelOrderTracking);
        }
    }

    protected static function setShipped($channelName, $updated, ChannelOrderTracking $channelOrderTracking)
    {
        if ($channelName !== 'Amazon') {
            if ($updated) {
                $channelOrderTracking->setShipped();
            }
        }
    }

    protected static function markOrdersAsShipped(Tracking $tracker)
    {
        foreach ($tracker->getChannels() as $channel) {
            TrackingController::markOrderAsShipped($channel);
        }
    }

    protected static function markOrderAsShipped($channel)
    {
        foreach ($channel->getOrders() as $order) {
            if ($order->getShipped()) {
                Ecommerce::dd("Order: " . $order->getOrderNumber() . " has shipped");
                Tracking::markAsShipped($order->getOrderNumber(), $channel->getChannelName());
            }
        }
    }
}
