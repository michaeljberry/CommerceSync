<?php

use Amazon\AmazonOrder;
use Ecommerce\Ecommerce;

use Amazon\AmazonClient;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

// AmazonOrder::parseOrders(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));
$array = [
    "SellerSKUList" => [
        "1",
        "2",
        "3"
    ]
];
Ecommerce::dd(
    // AmazonClient::amazonCurl(
        new \Amazon\API\FulfillmentInboundShipment\GetInboundGuidanceForSKU($array)
    // )
);

endClock($start);