<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'rev/revvar.php';

$start = startClock();
$user_id = 838;

$folder = '/home/chesbro_amazon';

$request = $revord->get_orders();
//\ecommerceclass\ecommerceclass::dd($request);

$revord->save_orders($request, $ecommerce, $ibmdata, $folder);

endClock($start);