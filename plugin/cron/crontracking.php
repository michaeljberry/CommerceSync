<?php

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

use ecommerce\Ecommerce;
use models\channels\Channel;
use models\channels\Tracking;
use models\channels\order\Order;

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
    $t = '';;
    if (!empty($item_id)) {
        echo "Item ID: $item_id<br>";
        $num_id = explode('-', $item_id);
        $item_id = $num_id[0];
        $trans_id = $num_id[1];
    }

    $trackingInfo = IBM::getTrackingNum($order_num, $channelNumbers);
    $tracking_id = '';
    $carrier = '';
    if (isset($trackingInfo['USPS'])) {
        $tracking_id = trim($trackingInfo['USPS']);
        $carrier = 'USPS';
    } elseif (isset($trackingInfo['UPS'])) {
        $tracking_id = trim($tracking_id['UPS']);
        $carrier = 'UPS';
    }
    echo "$channel: $order_num -> $tracking_id<br>";

    if (!empty($tracking_id)) {
        $response = '';
        $shipped = false;
        $success = false;
        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel . '<br>';
        $result = Tracking::updateTrackingNum($order_id, $tracking_id, $carrier);
        echo $result . '<br>';
        if (strtolower($channel) == 'bigcommerce') {
            //Update BC
//            $response = $bcord->update_bc_tracking($order_num, $tracking_id, $carrier);
//            Ecommerce::dd($response);
            if ($response) {
                $shipped = true;
            }
        } elseif (strtolower($channel) == 'ebay') {
            //Update Ebay
            $response = $ebord->update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id);
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
                $amazonOrdersThatHaveShipped[] = $order_num;
                $amazonTrackingXML .= $amord->updateTrackingInfo($order_num, $tracking_id, $carrier, $amazonOrderCount);
            }
            $amazonOrderCount++;
        } elseif (strtolower($channel) == 'reverb') {
            //Update Reverb
            $response = $revord->update_reverb_tracking($order_num, $tracking_id, $carrier, 'false');
            $successMessage = '"shipped"';
            if (strpos($response, $successMessage)) {
                $shipped = true;
            }
        } elseif (strtolower($channel) == 'walmart') {
            //Update Walmart
            $response = $wmord->updateWalmartTracking($order_num, $tracking_id, $carrier);
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
            $success = Order::markAsShipped($order_num, $channel);
        }
        if ($success) {
            echo $channel . '-> ' . $order_num . ': ' . $tracking_id . PHP_EOL . '<br>';
        }
    }
}

if (!empty($amazonTrackingXML)) {
    Ecommerce::dd($amazonTrackingXML);
    $response = $amord->update_amazon_tracking($amazonTrackingXML);
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