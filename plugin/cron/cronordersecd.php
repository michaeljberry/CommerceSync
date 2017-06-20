<?php
error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'ecd/ecdvar.php';

$start = startClock();
$user_id = 838;

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

endClock($start);