<?php

use Walmart\WalmartOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'wm/wmvar.php';

$start = startClock();
$user_id = 838;

$orders = $wmord->getOrders();

$wmord->parseOrders($orders);


//$orders = $wmord->getOrder('2578500230963');
//$wmord->parseOrder($orders);

endClock($start);