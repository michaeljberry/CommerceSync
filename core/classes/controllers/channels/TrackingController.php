<?php

namespace controllers\channels;

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
        if(null !== $tracker->getChannel('Amazon')) {
            Ecommerce::dd('Update Amazon Tracking');
            $tracker->getChannel('Amazon')->updateAmazonTracking($tracker->getChannel('Amazon'));
        }
        Ecommerce::dd($tracker);
        foreach($tracker->getChannels() as $channel){
            Ecommerce::dd('Channel:');
            Ecommerce::dd($channel);
            foreach($channel->getOrders() as $order){
                Ecommerce::dd('Order:');
                Ecommerce::dd($order->getOrderNumber());
                if($order->getShipped()) {
                    Ecommerce::dd("Order: " . $order->getOrderNumber() . " has shipped");
//                    Tracking::markAsShipped($order->getOrderNumber(), $channel->getChannelName());
                }
            }
        }
    }

    protected static function parseOrder(Tracking $tracker, $order, $method)
    {
        $orderNumber = $order['order_num'];
        $orderID = Order::getIdByOrder($orderNumber);
        $channelName = $order['type'];

        if($channelName == 'Walmart') {
        $tracker->setChannel($channelName);

            TrackingController::setTrackingNumbers($tracker, $channelName, $orderNumber, $orderID, $method);

            echo "$channelName: $orderNumber -> {$tracker->getTrackingNumber($channelName, $orderNumber)}<br>";
            $itemID = '';
            $transactionID = '';
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

            if (!empty($tracker->getTrackingNumber($channelName, $orderNumber))) {
                $trackingNumber = $tracker->getOrder($channelName, $orderNumber)->getTrackingNumber();
                $carrier = $tracker->getOrder($channelName, $orderNumber)->getCarrier();
//            $response = '';
//            $shipped = false;
//            $success = false;
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

                if ($channelName !== 'Amazon') {
                    if ($updated) {
                        $channelOrderTracking->setShipped();
                        echo "$channelName-> $orderNumber: $trackingNumber<br>" . PHP_EOL;
                    }
                }
            }




            if (strtolower($channelName) == 'bigcommerce') {
                //Update BC
//                $response = $channelOrderTracking->updateTracking($orderNumber, $trackingNumber, $carrier);

            } elseif (strtolower($channelName) == 'ebay') {
                //Update Ebay
//                $response = EbayOrderTracking::updateTracking($orderNumber, $trackingNumber, $carrier, $itemID,
//                    $transactionID);
//                $successMessage = 'Success';
//                if (strpos($response, $successMessage)) {
//                    $shipped = true;
//                }
            } elseif (strtolower($channelName) == 'amazon') {
//                if ($amazon_throttle) {
//                    echo 'Amazon is throttled.<br>';
//                } else {
//                    //Update Amazon
//                    $amazonOrdersThatHaveShipped[] = $orderNumber;
//                    $amazonTrackingXML .= AmazonOrderTracking::updateTracking($amazonTracking, $orderNumber,
//                        $trackingNumber, $carrier);
//                }
//                $amazonOrderCount++;
            } elseif (strtolower($channelName) == 'reverb') {
                //Update Reverb
//                $response = ReverbOrderTracking::updateTracking($orderNumber, $trackingNumber, $carrier);
//                $successMessage = '"shipped"';
//                if (strpos($response, $successMessage)) {
//                    $shipped = true;
//                }
            } elseif (strtolower($channelName) == 'walmart') {
                //Update Walmart
//                $response = WalmartOrderTracking::updateTracking($orderNumber, $trackingNumber, $carrier);
                //            Ecommerce::dd($response);
                //            if (array_key_exists('orderLineStatuses', $response['orderLines']['orderLine'])) {
                //                if (array_key_exists('trackingNumber', $response['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                //                    $shipped = true;
                //                }
                //            } elseif (array_key_exists('trackingNumber', $response['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                //                $shipped = true;
                //            }
            }
//            Ecommerce::dd($response);
//            if ($shipped) {
//                $success = Tracking::markAsShipped($orderNumber, $channelName);
//            }
//            if ($success) {
//                echo "$channelName-> $orderNumber: $trackingNumber<br>" . PHP_EOL;
//            }
//            return [$orderNumber, $channelName, $response, $successMessage, $amazonOrdersThatHaveShipped, $amazonTrackingXML];
        }
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


}
