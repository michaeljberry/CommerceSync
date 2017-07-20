<?php

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
//require WEBPLUGIN . 'am/amvar.php';
//require WEBPLUGIN . 'bc/bcvar.php';
//require WEBPLUGIN . 'eb/ebvar.php';
//require WEBPLUGIN . 'rev/revvar.php';
//require WEBPLUGIN . 'wm/wmvar.php';

use am\AmazonOrder;
use bc\BigCommerceOrder;
use eb\EbayOrder;
use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\Tracking;
use models\channels\order\Order;
use rev\ReverbOrder;
use wm\WalmartOrder;

//ob_start();

$start = startClock();
$user_id = 838;

$amazon_throttle = false;

$folder = ROOTFOLDER;
$logFileName = 'Tracking - ' . date('ymd') . '.txt';
$trackingLog = $folder . 'log/tracking/' . $logFileName;
echo "Tracking Numbers" . PHP_EOL;
echo "Channel -> Order Num : Tracking Number<br><br>" . PHP_EOL;

$unshippedOrders = Tracking::findUnshippedOrders();

$amazonOrderCount = 1;
$amazonTrackingXML = '';
$amazonOrdersThatHaveShipped = [];

foreach ($unshippedOrders as $order) {
    $orderNumber = $order['order_num'];
    $orderID = Order::getIdByOrder($orderNumber);
    $channel = $order['type'];
    $channelNumbers = Channel::getAccountNumbers($channel);
    $itemID = $order['item_id'];
    $t = '';;
    if (!empty($itemID)) {
        echo "Item ID: $itemID<br>";
        $num_id = explode('-', $itemID);
        $itemID = $num_id[0];
        $trans_id = $num_id[1];
    }

    $trackingInfo = IBM::getTrackingNum($orderNumber, $channelNumbers);
    $trackingNumber = '';
    $carrier = '';
    if (isset($trackingInfo['USPS'])) {
        $trackingNumber = trim($trackingInfo['USPS']);
        $carrier = 'USPS';
    } elseif (isset($trackingInfo['UPS'])) {
        $trackingNumber = trim($trackingNumber['UPS']);
        $carrier = 'UPS';
    }
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
            $response = EbayOrder::updateTracking($trackingNumber, $carrier, $itemID, $trans_id);
            $successMessage = 'Success';
            if (strpos($response, $successMessage)) {
                $shipped = true;
            }
        } elseif (strtolower($channel) == 'amazon') {
            if ($amazon_throttle) {
                echo 'Amazon is throttled.<br>';
                continue;
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
        if ($shipped) {
            $success = Order::markAsShipped($orderNumber, $channel);
        }
        if ($success) {
            echo "$channel-> $orderNumber: $trackingNumber<br>" . PHP_EOL;
        }
    }
}

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
endClock($start);
//$content = ob_get_contents();
//ob_end_clean();
//file_put_contents($inventory_log, $content, FILE_APPEND);