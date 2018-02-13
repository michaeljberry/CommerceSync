<?php

use Walmart\WalmartOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'wm/wmvar.php';

$start = startClock();

WalmartOrder::parseOrders(WalmartOrder::getOrders());

//$order = "2578500230963";
//WalmartOrder::parseOrder(WalmartOrder::getOrder($order));

endClock($start);
