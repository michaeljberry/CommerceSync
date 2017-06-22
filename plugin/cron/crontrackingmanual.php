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
use models\channels\TrackingModel;

//ob_start();

$start = startClock();
$user_id = 838;

$amazon_throttle = false;

$folder = ROOTFOLDER;
$log_file_name = 'Tracking - ' . date('ymd') . '.txt';
$tracking_log = $folder . 'log/tracking/' . $log_file_name;
echo "Tracking Numbers" . PHP_EOL;
echo "Channel -> Order Num : Tracking Number<br><br>" . PHP_EOL;

$unshippedOrders = TrackingModel::getUnshippedOrders();

$amazonOrderCount = 1;
$amazonTrackingXML = '';
$amazonOrdersThatHaveShipped = [];

foreach($unshippedOrders as $o){
    $order_num = $o['order_id'];
    $order_id = $ecommerce->getOrderId($order_num);
    $channel = $o['type'];
    $channelNumbers = $ecommerce->getChannelNumbers($channel);
    $item_id = $o['item_id'];
    $trans_id = '';
    if(!empty($item_id)){
        echo "Item ID: $item_id<br>";
        $num_id = explode('-',$item_id);
        $item_id = $num_id[0];
        $trans_id = $num_id[1];
    }

    $carrier = 'USPS';
    $tracking_id = trim($ibmdata->getManualTrackingNum($order_num));
    if(empty($tracking_id)){
        $tracking_id = trim($ibmdata->getSimilarTrackingNum($order_num));
    }
    echo "$channel: $order_num -> $tracking_id";

    if(!empty($tracking_id)) {
        $response = '';
        $shipped = false;
        $success = false;
        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel . '<br>';
        $result = $ecommerce->updateTrackingNum($order_id, $tracking_id, $carrier);
        echo $result . '<br>';
        if (strtolower($channel) == 'bigcommerce') {
            //update BC
            $response = $bcord->update_bc_tracking($order_num, $tracking_id, $carrier);
            if($response){
                $shipped = true;
            }
        } elseif (strtolower($channel) == 'ebay') {
            //update Ebay
            $response = $ebord->update_ebay_tracking($tracking_id, $carrier, $item_id, $trans_id);
            $successMessage = 'Success';
            if(strpos($response, $successMessage)){
                $success = true;
            }
        } elseif (strtolower($channel) == 'amazon') {
            if($amazon_throttle){
                echo 'Amazon is throttled.<br>';
                continue;
            }else {
                //Update Amazon
                $amazonOrdersThatHaveShipped[] = $order_num;
                $amazonTrackingXML .= $amord->updateTrackingInfo($order_num, $tracking_id, $carrier, $amazonOrderCount);
            }
            $amazonOrderCount++;
        } elseif (strtolower($channel) == 'reverb') {
            //Update Reverb
            $response = $revord->update_reverb_tracking($order_num, $tracking_id, $carrier, 'false');
            $successMessage = '"shipped"';
            if(strpos($response, $successMessage)){
                $success = true;
            }
        } elseif (strtolower($channel) == 'walmart'){
            //Update Walmart
            $response = $wmord->update_walmart_tracking($wm_consumer_key, $wm_secret_key, $wm_api_header, $order_num, $tracking_id, $carrier);
            if(array_key_exists('orderLineStatuses', $response['orderLines']['orderLine'])) {
                if (array_key_exists('trackingNumber', $response['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['trackingInfo'])) {
                    $shipped = true;
                }
            }elseif(array_key_exists('trackingNumber', $response['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['trackingInfo'])){
                $shipped = true;
            }
        }
        Ecommerce::dd($response);
        if($shipped){
            $success = $ecommerce->markAsShipped($order_num, $channel);
        }
        if($success) {
            echo $channel . '-> ' . $order_num . ': ' . $tracking_id . PHP_EOL . '<br>';
        }
    }
}

if(!empty($amazonTrackingXML)){
    Ecommerce::dd($amazonTrackingXML);
    $response = $amord->update_amazon_tracking($amazonTrackingXML);
    print_r($response);
    echo '<br>';
    $successMessage = 'SUBMITTED';
    if (strpos($response, $successMessage)) {
        foreach($amazonOrdersThatHaveShipped as $order_num) {
            $success = $ecommerce->markAsShipped($order_num, $channel);
        }
    }elseif(strpos($response, 'throttle') || strpos($response, 'QuotaExceeded')){
        $amazon_throttle = true;
        echo 'Amazon is throttled.<br>';
    }
}
endClock($start);
//$content = ob_get_contents();
//ob_end_clean();
//file_put_contents($inventory_log, $content, FILE_APPEND);