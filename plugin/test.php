<?php

use Amazon\AmazonOrder;
use Ecommerce\Ecommerce;

use AmazonMWSAPI\AmazonClient;
use AmazonMWSAPI\FulfillmentInventory;
use AmazonMWSAPI\Helpers\Helpers;
use Guzzle\Tests\Batch\BatchClosureDivisorTest;

error_reporting(-1);

require __DIR__ . '/../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

// Ecommerce::dd(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));

// Ecommerce::getDirContents('../../vendor/michaeljberry/amazon-mws-api/src');

$iterations = 1;

$testPerformance = false;

$amazonCurlTest = false;

$objectToNewUp = "\AmazonMWSAPI\FulfillmentInboundShipment\CreateInboundShipment";

$objectParameters = [
    "ShipmentId" => "1234567890",
    "InboundShipmentHeader" => [
        "ShipmentName" => "Blah",
        "ShipFromAddress" => [
            "Name" => "Ben Parker",
            "AddressLine1" => "1234 Main St.",
            "City" => "New York",
            "CountryCode" => "US"
        ],
        "DestinationFulfillmentCenterId" => "987654321",
        "LabelPrepPreference" => "SELLER_LABEL",
        "AreCasesRequired" => "false",
        "ShipmentStatus" => "WORKING",
    ],
    "InboundShipmentItems" => [
        [
            "SellerSKU" => "M150",
            "QuantityShipped" => 10,
            "QuantityInCase" => 5,
            "ReleaseDate" => "2018-01-05",
            "PrepDetailsList" => [
                [
                    "PrepInstruction" => "Labeling",
                    "PrepOwner" => "SELLER"
                ]
            ]
        ],
        [
            "SellerSKU" => "M180",
            "QuantityShipped" => 4,
            "QuantityInCase" => 2,
            "ReleaseDate" => "2018-01-05",
            "PrepDetailsList" => [
                [
                    "PrepInstruction" => "Labeling",
                    "PrepOwner" => "SELLER"
                ],
                [
                    "PrepInstruction" => "Polybagging",
                    "PrepOwner" => "SELLER"
                ]
            ]
        ]
    ]
];

if ($amazonCurlTest) {

    Helpers::testAPI($objectToNewUp, $objectParameters, $testPerformance, $iterations);

} else {

    Helpers::test($objectToNewUp, $objectParameters, $testPerformance, $iterations);

}

endClock($start);