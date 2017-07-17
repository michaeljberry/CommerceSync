<?php
error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'am/amvar.php';

$start = startClock();
$userId = 838;
$companyId = 1;
$folder = '/home/chesbro_amazon/';

$orders = $amord->getOrders();

$amord->parseOrders($orders, $folder, $companyId);

endClock($start);