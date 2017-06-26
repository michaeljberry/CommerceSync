<?php
error_reporting(-1);

include __DIR__ . '/../../core/init.php';
include WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'eb/ebvar.php';

use eb\EbayClient;

$start = startClock();
$user_id = 838;

$ebayDays = $ebord->get_order_days(EbayClient::getStoreID());

$folder = '/home/chesbro_amazon/';

$pagenumber = 1;
$requestName = 'GetOrders';

$ebord->getOrders($requestName, $pagenumber, $ebayDays, $folder, $ecommerce, $ibmdata);

endClock($start);