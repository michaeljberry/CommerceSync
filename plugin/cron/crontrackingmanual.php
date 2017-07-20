<?php

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

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
$log_file_name = 'Tracking - ' . date('ymd') . '.txt';
$tracking_log = $folder . 'log/tracking/' . $log_file_name;
echo "Tracking Numbers" . PHP_EOL;
echo "Channel -> Order Num : Tracking Number<br><br>" . PHP_EOL;

$unshippedOrders = Tracking::findUnshippedOrders();

$amazonOrderCount = 1;
$amazonTrackingXML = '';
$amazonOrdersThatHaveShipped = [];

foreach ($unshippedOrders as $o) {
    $order_num = $o['order_num'];
    $order_id = Order::getIdByOrder($order_num);
    $channel = $o['type'];
    $channelNumbers = Channel::getAccountNumbers($channel);
    $item_id = $o['item_id'];
    $trans_id = '';
    if (!empty($item_id)) {
        echo "Item ID: $item_id<br>";
        $num_id = explode('-', $item_id);
        $item_id = $num_id[0];
        $trans_id = $num_id[1];
    }

    $carrier = 'USPS';
    $tracking_id = trim(IBM::getManualTrackingNum($order_num, $channelNumbers));
    if (empty($tracking_id)) {
        $tracking_id = trim(IBM::getSimilarTrackingNum($order_num, $channelNumbers));
    }
    echo "$channel: $order_num -> $tracking_id";

    if (!empty($tracking_id)) {
        $response = '';
        $shipped = false;
        $success = false;
        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel . '<br>';
        $result = Tracking::updateTrackingNum($order_id, $tracking_id, $carrier);
        echo $result . '<br>';
        if (strtolower($channel) == 'bigcommerce') {
            //update BC
            $response = BigCommerceOrder::updateTracking($order_num, $tracking_id, $carrier);
            if ($response) {
                $shipped = true;
            }
        } elseif (strtolower($channel) == 'ebay') {
            //update Ebay
            $response = EbayOrder::updateTracking($tracking_id, $carrier, $item_id, $trans_id);
            $successMessage = 'Success';
            if (strpos($response, $successMessage)) {
                $success = true;
            }
        } elseif (strtolower($channel) == 'amazon') {
            if ($amazon_throttle) {
                echo 'Amazon is throttled.<br>';
                continue;
            } else {
                //Update Amazon
                $amazonOrdersThatHaveShipped[] = $order_num;
                $amazonTrackingXML .= AmazonOrder::updateTrackingInfo($order_num, $tracking_id, $carrier,
                    $amazonOrderCount);
            }
            $amazonOrderCount++;
        } elseif (strtolower($channel) == 'reverb') {
            //Update Reverb
            $response = ReverbOrder::updateTracking($order_num, $tracking_id, $carrier, 'false');
            $successMessage = '"shipped"';
            if (strpos($response, $successMessage)) {
                $success = true;
            }
        } elseif (strtolower($channel) == 'walmart') {
            //Update Walmart
            $response = WalmartOrder::updateTracking($order_num, $tracking_id, $carrier);
            if (array_key_exists('orderLineStatuses', $response['orderLines']['orderLine'])) {
                if (array_key_exists('trackingNumber', $response['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                    $shipped = true;
                }
            } elseif (array_key_exists('trackingNumber', $response['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                $shipped = true;
            }
        }
        Ecommerce::dd($response);
        if ($shipped) {
            $success = Order::markAsShipped($order_num, $channel);
        }
        if ($success) {
            echo $channel . '-> ' . $order_num . ': ' . $tracking_id . PHP_EOL . '<br>';
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
        foreach ($amazonOrdersThatHaveShipped as $order_num) {
            $success = Order::markAsShipped($order_num, $channel);
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