<?php
use am\AmazonOrder;

error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'am/amvar.php';

$start = startClock();
$userId = 838;
$companyId = 1;

$orders = AmazonOrder::getOrders();

$amord->parseOrders($orders);

endClock($start);