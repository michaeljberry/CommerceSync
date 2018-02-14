<?php

use Ebay\EbayOrder;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'eb/ebvar.php';

$start = startClock();

EbayOrder::parseOrders(EbayOrder::getOrders());

endClock($start);