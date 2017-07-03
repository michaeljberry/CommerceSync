<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'rev/revvar.php';

$start = startClock();
$user_id = 838;

$folder = '/home/chesbro_amazon/';

$request = $revord->getOrders();
//\ecommerceclass\ecommerceclass::dd($request);

$revord->saveOrders($request, $ecommerce, $folder);

endClock($start);