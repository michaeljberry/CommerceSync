<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'rev/revvar.php';
$request = $revord->get_orders();
//\ecommerceclass\ecommerceclass::dd($request);

$revord->save_orders($request, $ecommerce, $ibmdata);

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";