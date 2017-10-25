<?php

use Reverb\ReverbOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'rev/revvar.php';

$start = startClock();
$user_id = 838;

$orders = ReverbOrder::getOrders();

$revord->parseOrders($orders);

endClock($start);
