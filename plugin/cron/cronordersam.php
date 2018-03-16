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
// Ecommerce::ddXml(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));
$array = [
    "InboundShipmentPlanRequestItems" => [
        [
            "SellerSKU" => "SKU1",
            "Quantity" => 1
        ],
        [
            "SellerSKU" => "SKU2",
            "Quantity" => 1,
            "PrepDetailsList" => [
                "PrepInstruction" => "Taping",
                "PrepOwner" => "AMAZON"
            ]
        ]
    ],
    "ShipFromAddress" => [
        "Name" => "Ben Parker",
        "AddressLine1" => "123 Main St.",
        "City" => "New York",
        "CountryCode" => "US"
    ]
];
Ecommerce::dd(
    // AmazonClient::amazonCurl(
        new \Amazon\API\FulfillmentInboundShipment\CreateInboundShipmentPlan($array)
    // )
);

endClock($start);