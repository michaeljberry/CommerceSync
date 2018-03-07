<?php

use Amazon\AmazonOrder;
use Ecommerce\Ecommerce;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

AmazonOrder::parseOrders(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));

// Ecommerce::ddXml(
//     \Amazon\AmazonInventory::updateInventory(
//         \Amazon\AmazonInventory::priceArray(
//             "KG6W",
//             "19.99"
//         )
//     )
// );

endClock($start);