<?php
error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'ecd/ecdvar.php';

$response = $ecdord->get_orders($ecd_ocp_key, $ecd_sub_key);

print_r($response);

$orders = json_decode($response, true);
echo '<br><br>';
print_r($orders);

//foreach ($orders['data'] as $o){
//    print_r($o);
//    echo '<br><br>';
//    $ecomdashId = $o['EcomdashId'];
//}