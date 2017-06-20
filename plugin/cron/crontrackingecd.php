<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';
require WEBPLUGIN . 'ecd/ecdvar.php';

$orders_last_day = \ecommerce\channelHelperController::getOrdersInLastDay();
$folder = '/var/www/html/portal/';
$log_file_name = 'Tracking - ' . date('ymd') . '.txt';
$tracking_log = $folder . 'log/tracking/' . $log_file_name;
$orders_with_tracking = [];

foreach($orders_last_day as $o){
    $order_num = $o['order_id'];
//    if($order_num != '3578146605518'){
//        continue;
//    }
    $order_id = $ecommerce->get_order_id($order_num);
    $channel = $o['type'];
    $item_id = $o['item_id'];
    $trans_id = '';
    if(!empty($item_id)){
        $num_id = explode('-',$item_id);
        $item_id = $num_id[0];
        $trans_id = $num_id[1];
    }
    $carrier = '';
//    echo '<br>' . $channel . ': ' . $order_num . '-> ';
    $tracking_id = trim($ibmdata->get_usps_tracking_num($order_num));
    if(empty($tracking_id)){
        $tracking_id = trim($ibmdata->get_ups_tracking_num($order_num));
        $carrier = 'UPS';
    }else{
        $carrier = 'USPS';
    }
    if(!empty($tracking_id)) {
        $success = false;
        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel;
        $result = $ecommerce->update_tracking_num($order_id, $tracking_id, $carrier);
        echo "Tracking ID: $result<br>";
        $orders_with_tracking[$order_num] = [
            'Channel' => $channel,
            'Tracking' => $tracking_id,
            'Carrier' => $carrier
        ];
    }
}
echo '<br><br>';
print_r($orders_with_tracking);
echo '<br><br>';
$orders = json_decode($ecdord->get_orders($ecd_ocp_key, $ecd_sub_key));
//print_r($orders->data);
foreach($orders->data as $o){
    $orderId = $o->EcomdashId;
    $orderNum = $o->StorefrontOrderNumber;
    $storeFront = $o->StorefrontType;
    echo "<br>$orderId -> $orderNum -> $storeFront<br>";
//    if(array_key_exists($orderNum, $orders_with_tracking)) {
//        $shipment = json_decode($ecdord->create_shipment($ecd_ocp_key, $ecd_sub_key, $orderId));
//        foreach($shipment->data as $s){
//            $shipmentId = $s->Items->ShipmentId;
//            $lineItemId = $s->Items->Id;
//            $tracking = $ecdord->update_tracking_num($ecd_ocp_key, $ecd_sub_key, $lineItemId, $shipmentId, $orders_with_tracking[$orderNum]['Carrier'], $orders_with_tracking[$orderNum]['Tracking']);
//            $success = $tracking->data->WasSuccessful;
//            if($success == 'true'){
//                $ecommerce->update_tracking_successful($orderNum);
//                echo "Tracking for order $order_num was updated!<br><br>";
//            }
//        }
//    }
}
endClock($start);