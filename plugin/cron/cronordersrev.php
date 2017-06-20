<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'rev/revvar.php';
$request = $revord->get_orders();
//\ecommerceclass\ecommerceclass::dd($request);

$revord->save_orders($request, $ecommerce, $ibmdata);

endClock($start);