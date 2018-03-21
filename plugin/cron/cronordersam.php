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
// Ecommerce::dd(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));
// $array = [
    //     "InboundShipmentPlanRequestItems" => [
        //         [
            //             "SellerSKU" => "SKU2",
            //             "Quantity" => 1,
//             "PrepDetailsList" => [
    //                 "PrepInstruction" => "Taping",
    //                 "PrepOwner" => "AMAZON"
    //             ]
    //         ]
    //     ],
//     "ShipFromAddress" => [
    //         "Name" => "Ben Parker",
    //         "AddressLine1" => "123 Main St.",
    //         "City" => "New York",
    //         "CountryCode" => "IN"
//     ],
//     "LabelPrepPreference" => "SELLER_LABEL",
//     "ShipToCountryCode" => "US",
//     // "ShipToCountrySubdivisionCode" => "IN"
//     // "RandomParameter" => "Blah"
// ];
// Ecommerce::dd(
    //     // AmazonClient::amazonCurl(
        //         new \Amazon\API\FulfillmentInboundShipment\CreateInboundShipmentPlan($array)
        //     // )
        // );
Ecommerce::ddXml(
    AmazonClient::amazonCurl(
        new \Amazon\API\Feeds\CancelFeedSubmissions()
    )
);

endClock($start);