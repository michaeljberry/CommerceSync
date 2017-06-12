<?php
error_reporting(-1);
include __DIR__ . '/../../core/init.php';
include WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'wm/wmvar.php';

$wmorder = $wmord->construct_auth($wm_consumer_key, $wm_secret_key, $wm_api_header);

function parseOrder($o, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata){
//    \ecommerceclass\ecommerceclass::dd($o);
    $order_num = $o['purchaseOrderId'];
    echo "Order: $order_num<br><br>";
    $found = $ecommerce->orderExists($order_num);
    if (!$found) {
        $acknowledged = $wmord->acknowledge_order($wm_consumer_key, $wm_secret_key, $wm_api_header, $o);
//        echo 'Acknowledgement: <br><pre>';
//        print_r($acknowledged);
//        echo '</pre><br><br>';
        if ((array_key_exists('orderLineStatuses', $acknowledged['orderLines']['orderLine']) &&
            $acknowledged['orderLines']['orderLine']['orderLineStatuses']['orderLineStatus']['status'] == 'Acknowledged')
         || $acknowledged['orderLines']['orderLine'][0]['orderLineStatuses']['orderLineStatus']['status'] == 'Acknowledged') {
            $wmord->get_wm_order($wm_consumer_key, $wm_secret_key, $ecommerce, $wm_store_id, $ibmdata, $o);
        }
    }
}
function getOrders($wmorder, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata, $next = null)
{
    try {
        $fromDate = '-3 days';
        if(!empty($next)){
            $orders = $wmorder->list([
                'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
                'nextCursor' => $next
            ]);
        }else {
            $orders = $wmorder->listAll([
                'createdStartDate' => date('Y-m-d', strtotime($fromDate)),
//                'limit' => 200
            ]);
        }

        echo 'Orders: <br>';
//        \ecommerceclass\ecommerceclass::dd($orders);
        $totalCount = $orders['meta']['totalCount'];
//        $nextCursor = $orders['meta']['nextCursor'];
        echo 'Order Count: ' . count($orders['elements']) . '<br><br>';

        \ecommerce\Ecommerce::dd($orders['elements']['order']);

        if (count($orders['elements']['order']) > 1) { // if there are multiple orders to pull **DO NOT CHANGE**
            foreach ($orders['elements']['order'] as $o) {
                parseOrder($o, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata);
            }
        } else {
            foreach ($orders['elements'] as $o) {
                parseOrder($o, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata);
            }
        }
//        if($totalCount > 10){ // && !empty($nextCursor)
//            getOrders($wmorder, $db, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata); //$nextCursor
//        }
    } catch (Exception $e) {
        die("There was a problem requesting the data: " . $e->getMessage());
    }
}

getOrders($wmorder, $ecommerce, $wmord, $wm_consumer_key, $wm_secret_key, $wm_api_header, $wm_store_id, $ibmdata);