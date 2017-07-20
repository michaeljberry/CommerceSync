<?php

use eb\EbayOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'eb/ebvar.php';

$start = startClock();
$userID = 838;
$pageNumber = 1;

$requestName = 'GetOrders';

$orders = EbayOrder::getOrders($requestName, $pageNumber);

$ebord->parseOrders($orders, $pageNumber, $requestName);

endClock($start);