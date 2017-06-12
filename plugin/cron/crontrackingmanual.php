<?php

use ecommerce\Ecommerce as ecom;
use controllers\channels\TrackingController;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

$amazon_throttle = false;

$folder = ROOTFOLDER;
$log_file_name = 'Tracking - ' . date('ymd') . '.txt';
$tracking_log = $folder . 'log/tracking/' . $log_file_name;
echo "Tracking Numbers" . PHP_EOL;
echo "Channel -> Order Num : Tracking Number<br><br>" . PHP_EOL;

$unshippedOrders = TrackingController::getUnshippedOrders();

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
        $success = false;
        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel . '<br>';
        $result = $ecommerce->updateTrackingNum($order_id, $tracking_id, $carrier);
        echo $result . '<br>';
        if (strtolower($channel) == 'bigcommerce') {
            //update BC
            $response = $bcord->update_bc_tracking($order_num, $tracking_id, $carrier);
            if($response){
                $ecommerce->markAsShipped($order_num, $channel);
                echo 'Tracking for MML order ' . $order_num . ' was updated!<br><br>';
                $success = true;
            }
        } elseif (strtolower($channel) == 'ebay') {
            //update Ebay
            $response = $ebord->update_ebay_tracking($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $tracking_id, $carrier, $item_id, $trans_id);
//            print_r($response);
//            echo '<br>';
            if(strpos($response, 'Success')){
                $ecommerce->markAsShipped($order_num, $channel);
                echo 'Tracking for eBay order ' . $order_num . ' was updated!<br><br>';
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
            $response = $revord->update_reverb_tracking($order_num, $tracking_id, $carrier, 'false');
            print_r($response);
            echo '<br>';
            if(strpos($response, '"shipped"')){
                $ecommerce->markAsShipped($order_num, $channel);
                echo 'Tracking for Reverb order ' . $order_num . ' was updated!<br><br>';
                $success = true;
            }
        } elseif (strtolower($channel) == 'walmart'){
//            if($order_num == '4578065682141') {
//                try {
//                    $response = $wmord->update_walmart_tracking($wm_consumer_key, $wm_secret_key, $wm_api_header, $order_num, $tracking_id, $carrier);
//                    print_r($response);
//                    echo '<br>';
//                if (strpos($response, '"status":"shipped"')) {
//                    $ecommerce->update_tracking_successful($order_num);
//                    echo 'Tracking for Walmart order ' . $order_num . ' was updated!<br><br>';
//                    $success = true;
//                }
//                }catch(Exception $e){
//                    die("There was a problem requesting the data: " . $e->getMessage());
//                }
//            }
        }
        if($success) {
            echo $channel . '-> ' . $order_num . ': ' . $tracking_id . PHP_EOL;
        }
    }
}
if(!empty($amazonTrackingXML)){
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
$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";
echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";
//$content = ob_get_contents();
//ob_end_clean();
//file_put_contents($inventory_log, $content, FILE_APPEND);