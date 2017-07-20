<?php

use bc\BigCommerceOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'bc/bcvar.php';

$start = startClock();
$user_id = 838;

$orders = BigCommerceOrder::getOrders($BC);

if(!empty($orders)) {
    $bcord->parseOrders($orders);
}

endClock($start);