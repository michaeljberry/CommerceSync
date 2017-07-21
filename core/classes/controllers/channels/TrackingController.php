<?php

namespace controllers\channels;

use am\AmazonOrder;
use bc\BigCommerceOrder;
use eb\EbayOrder;
use ecommerce\Ecommerce;
use IBM;
use models\channels\Channel;
use models\channels\order\Order;
use models\channels\Tracking;
use rev\ReverbOrder;
use wm\WalmartOrder;

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
        $orders = TrackingController::getUnshippedOrders();
        TrackingController::parseOrders($orders, $method);
    }

    public static function getUnshippedOrders()
    {
        return Tracking::findUnshippedOrders();
    }

    protected static function parseOrders($orders, $method)
    {
        $amazon_throttle = false;
        $amazonOrderCount = 1;
        $amazonTrackingXML = '';
        $amazonOrdersThatHaveShipped = [];

        foreach ($orders as $order) {
            list($orderNumber, $channel, $response, $successMessage, $amazonOrdersThatHaveShipped, $amazonTrackingXML) = TrackingController::parseOrder($order,
                $amazon_throttle, $amazonOrdersThatHaveShipped, $amazonOrderCount, $amazonTrackingXML, $method);
        }

        TrackingController::updateAmazonTracking($amazonTrackingXML, $amazonOrdersThatHaveShipped, $channel);
    }

    /**
     * @param $order
     * @param $amazon_throttle
     * @param $amazonOrdersThatHaveShipped
     * @param $amazonOrderCount
     * @param $amazonTrackingXML
     * @param $method
     * @return array
     */
    protected static function parseOrder(
        $order,
        $amazon_throttle,
        $amazonOrdersThatHaveShipped,
        $amazonOrderCount,
        $amazonTrackingXML,
        $method
    ) {
        $orderNumber = $order['order_num'];
        $orderID = Order::getIdByOrder($orderNumber);
        $channel = $order['type'];
        $channelNumbers = Channel::getAccountNumbers($channel);
        $itemID = $order['item_id'];
        $transID = '';
        if (!empty($itemID)) {
            echo "Item ID: $itemID<br>";
            $numID = explode('-', $itemID);
            $itemID = $numID[0];
            $transID = $numID[1];
        }

        list($trackingNumber, $carrier) = TrackingController::getTrackingNumbers($orderNumber, $channelNumbers, ,
            $method);
        echo "$channel: $orderNumber -> $trackingNumber<br>";

        if (!empty($trackingNumber)) {
            $response = '';
            $shipped = false;
            $success = false;
            echo $orderID . ': ' . $trackingNumber . '; Channel: ' . $channel . '<br>';
            $result = Tracking::updateTrackingNum($orderID, $trackingNumber, $carrier);
            echo $result . '<br>';
            if (strtolower($channel) == 'bigcommerce') {
                //Update BC
                $response = BigCommerceOrder::updateTracking($orderNumber, $trackingNumber, $carrier);
                Ecommerce::dd($response);
                if ($response) {
                    $shipped = true;
                }
            } elseif (strtolower($channel) == 'ebay') {
                //Update Ebay
                $response = EbayOrder::updateTracking($trackingNumber, $carrier, $itemID, $transID);
                $successMessage = 'Success';
                if (strpos($response, $successMessage)) {
                    $shipped = true;
                }
            } elseif (strtolower($channel) == 'amazon') {
                if ($amazon_throttle) {
                    echo 'Amazon is throttled.<br>';
                } else {
                    //Update Amazon
                    $amazonOrdersThatHaveShipped[] = $orderNumber;
                    $amazonTrackingXML .= AmazonOrder::updateTrackingInfo($orderNumber, $trackingNumber, $carrier,
                        $amazonOrderCount);
                }
                $amazonOrderCount++;
            } elseif (strtolower($channel) == 'reverb') {
                //Update Reverb
                $response = ReverbOrder::updateTracking($orderNumber, $trackingNumber, $carrier, 'false');
                $successMessage = '"shipped"';
                if (strpos($response, $successMessage)) {
                    $shipped = true;
                }
            } elseif (strtolower($channel) == 'walmart') {
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
                $success = Order::markAsShipped($orderNumber, $channel);
            }
            if ($success) {
                echo "$channel-> $orderNumber: $trackingNumber<br>" . PHP_EOL;
            }
        }

        return [$orderNumber, $channel, $response, $successMessage, $amazonOrdersThatHaveShipped, $amazonTrackingXML];
    }

    /**
     * @param $amazonTrackingXML
     * @param $amazonOrdersThatHaveShipped
     * @param $channel
     */
    protected static function updateAmazonTracking($amazonTrackingXML, $amazonOrdersThatHaveShipped, $channel)
    {
        if (!empty($amazonTrackingXML)) {
            Ecommerce::dd($amazonTrackingXML);
            $response = AmazonOrder::updateTracking($amazonTrackingXML);
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

    /**
     * @param $orderNumber
     * @param $channelNumbers
     * @param $method
     * @return array
     */
    protected static function getTrackingNumbers($orderNumber, $channelNumbers, $method)
    {
        $trackingNumber = '';
        $carrier = '';

        if($method !== 'auto'){
            $carrier = 'USPS';
            $trackingNumber = trim(IBM::getManualTrackingNum($orderNumber, $channelNumbers));
            if (empty($trackingNumber)) {
                $trackingNumber = trim(IBM::getSimilarTrackingNum($orderNumber, $channelNumbers));
            }
            return [$trackingNumber, $carrier];
        }

        $trackingInfo = IBM::getTrackingNum($orderNumber, $channelNumbers);
        if (isset($trackingInfo['USPS'])) {
            $trackingNumber = trim($trackingInfo['USPS']);
            $carrier = 'USPS';
        } elseif (isset($trackingInfo['UPS'])) {
            $trackingNumber = trim($trackingNumber['UPS']);
            $carrier = 'UPS';
        }

        return [$trackingNumber, $carrier];
    }


}