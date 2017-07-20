<?php

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'bc/bcvar.php';

$start = startClock();
$user_id = 838;

$orders = $bcord->getOrders($BC, $ecommerce);

$bcord->parseOrders($orders);

endClock($start);