<?php
error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'am/amvar.php';

$startTime = microtime(true);
$userId = 838;
$companyId = 1;
$folder = '/home/chesbro_amazon/';

$taxableStates = $ecommerce->getCompanyTaxInfo($companyId);
//\ecommerceclass\ecommerceclass::dd($taxableStates);

$orders = $amord->getOrders();
//\ecommerceclass\ecommerceclass::dd($orders);

$amord->parseOrders($orders, $ecommerce, $ibmdata, $folder, $companyId);

$endTime = microtime(true);
$executionTime = ($endTime - $startTime)/60;
echo "Execution time: $executionTime mins";