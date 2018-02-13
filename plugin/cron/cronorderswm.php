<?php

use Walmart\WalmartOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'wm/wmvar.php';

$start = startClock();

WalmartOrder::parseOrders(WalmartOrder::getOrders());

//$orders = $wmord->getOrder('2578500230963');
//$wmord->parseOrder($orders);

endClock($start);
