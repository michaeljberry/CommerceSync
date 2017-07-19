<?php
use am\AmazonOrder;

error_reporting(-1);

include __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();
$userID = 838;

$orders = AmazonOrder::getOrders();

$amord->parseOrders($orders);

endClock($start);