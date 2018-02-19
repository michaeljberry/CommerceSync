<?php

use Amazon\AmazonOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

AmazonOrder::parseOrders(AmazonOrder::getOrders());

endClock($start);