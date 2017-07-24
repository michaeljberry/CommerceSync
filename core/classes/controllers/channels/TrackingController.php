<?php

namespace controllers\channels;

use Amazon\AmazonOrder;
use BigCommerce\BigCommerceOrder;
use Ebay\EbayOrder;
use ecommerce\Ecommerce;
use IBM;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\Tracking;
use Reverb\ReverbOrder;
use Walmart\WalmartOrder;

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
        $amazon_throttle = false;
        $amazonOrderCount = 1;
        $amazonTrackingXML = '';
        $amazonOrdersThatHaveShipped = [];

        foreach ($orders as $order) {
            list($orderNumber, $channel, $response, $successMessage, $amazonOrdersThatHaveShipped, $amazonTrackingXML) = TrackingController::parseOrder($tracker, $order,
                $amazon_throttle, $amazonOrdersThatHaveShipped, $amazonOrderCount, $amazonTrackingXML, $method);
        }
        Ecommerce::dd($tracker);
//        TrackingController::updateAmazonTracking($amazonTrackingXML, $amazonOrdersThatHaveShipped, $channel);
    }

    protected static function parseOrder(
        Tracking $tracker,
        $order,
        $amazon_throttle,
        $amazonOrdersThatHaveShipped,
        $amazonOrderCount,
        $amazonTrackingXML,
        $method
    ) {
        $orderNumber = $order['order_num'];
        $orderID = Order::getIdByOrder($orderNumber);
        $channelName = $order['type'];
        if($channelName == 'Amazon') {
            $tracker->setChannelName($channelName);

            TrackingController::setTrackingNumbers($tracker, $channelName, $orderNumber, $method);

            echo "$channelName: $orderNumber -> {$tracker->getTrackingNumber($channelName, $orderNumber)}<br>";
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

        return;

        if (!empty($tracker->getTrackingNumber($channelName, $orderNumber))) {
            $response = '';
            $shipped = false;
            $success = false;
            echo $orderID . ': ' . $trackingNumber . '; Channel: ' . $channelName . '<br>';
            $result = Tracking::updateTrackingNumber($orderID, $trackingNumber, $carrier);
            echo $result . '<br>';
            if (strtolower($channelName) == 'bigcommerce') {
                //Update BC
                $response = BigCommerceOrder::updateTracking($orderNumber, $trackingNumber, $carrier);
                Ecommerce::dd($response);
                if ($response) {
                    $shipped = true;
                }
            } elseif (strtolower($channelName) == 'ebay') {
                //Update Ebay
                $response = EbayOrder::updateTracking($trackingNumber, $carrier, $itemID, $transactionID);
                $successMessage = 'Success';
                if (strpos($response, $successMessage)) {
                    $shipped = true;
                }
            } elseif (strtolower($channelName) == 'amazon') {
                if ($amazon_throttle) {
                    echo 'Amazon is throttled.<br>';
                } else {
                    //Update Amazon
                    $amazonOrdersThatHaveShipped[] = $orderNumber;
                    $amazonTrackingXML .= AmazonOrder::updateTracking($orderNumber, $trackingNumber, $carrier, $amazonOrderCount);
                }
                $amazonOrderCount++;
            } elseif (strtolower($channelName) == 'reverb') {
                //Update Reverb
                $response = ReverbOrder::updateTracking($orderNumber, $trackingNumber, $carrier, 'false');
                $successMessage = '"shipped"';
                if (strpos($response, $successMessage)) {
                    $shipped = true;
                }
            } elseif (strtolower($channelName) == 'walmart') {
                //Update Walmart
                $response = WalmartOrder::updateTracking($orderNumber, $trackingNumber, $carrier);
                //            Ecommerce::dd($response);
                //            if (array_key_exists('orderLineStatuses', $response['orderLines']['orderLine'])) {
                //                if (array_key_exists('trackingNumber', $response['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                //                    $shipped = true;
                //                }
                //            } elseif (array_key_exists('trackingNumber', $response['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                //                $shipped = true;
                //            }
            }
            Ecommerce::dd($response);
            if ($shipped) {
                $success = Order::markAsShipped($orderNumber, $channelName);
            }
            if ($success) {
                echo "$channelName-> $orderNumber: $trackingNumber<br>" . PHP_EOL;
            }
        }

        return [$orderNumber, $channelName, $response, $successMessage, $amazonOrdersThatHaveShipped, $amazonTrackingXML];
    }

    protected static function updateAmazonTracking($amazonTrackingXML, $amazonOrdersThatHaveShipped, $channel)
    {
        if (!empty($amazonTrackingXML)) {
            Ecommerce::dd($amazonTrackingXML);
            $response = AmazonOrder::sendTracking($amazonTrackingXML);
            print_r($response);
            echo '<br>';
            $successMessage = 'SUBMITTED';
            if (strpos($response, $successMessage)) {
                foreach ($amazonOrdersThatHaveShipped as $orderNumber) {
                    $success = Order::markAsShipped($orderNumber, $channel);
                }
            } elseif (strpos($response, 'throttle') || strpos($response, 'QuotaExceeded')) {
                $amazon_throttle = true;
                echo 'Amazon is throttled.<br>';
            }
        }
    }

    protected static function setTrackingNumbers(Tracking $tracker, $channelName, $orderNumber, $method)
    {
        $trackingNumber = '';
        $carrier = '';

        if($method !== 'auto'){
            $carrier = 'USPS';
            $trackingNumber = trim(IBM::getManualTrackingNum($orderNumber, $tracker->getChannelNumbers($channelName)));
            if (empty($trackingNumber)) {
                $trackingNumber = trim(IBM::getSimilarTrackingNum($orderNumber, $tracker->getChannelNumbers($channelName)));
            }
            $tracker->setOrderTracking($channelName, $orderNumber, $trackingNumber, $carrier);
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
        $tracker->setOrderTracking($channelName, $orderNumber, $trackingNumber, $carrier);

        return;
    }


}