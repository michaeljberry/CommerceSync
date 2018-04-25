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

$start = Helpers::startClock();

// Ecommerce::dd(AmazonOrder::getUnshippedOrders());
// Ecommerce::ddXml(AmazonOrder::getOrderById("112-4364971-2410668"));

// Ecommerce::getDirContents('../../vendor/michaeljberry/amazon-mws-api/src');

$iterations = 1;

$testPerformance = false;

$print = true;

$amazonCurlTest = false;

$api = "AmazonMWSAPI";

$apiToTest = "FulfillmentInboundShipment";

$classToTest = "CreateInboundShipmentPlan";

$property = "exampleCreateInboundShipmentPlanFailing";

$objectToNewUp = "\\";
$objectToNewUp .= $api;
$objectToNewUp .= "\\";
$objectToNewUp .= "$apiToTest";
$objectToNewUp .= "\\";
$objectToNewUp .= "$classToTest";

$objectParameters = Helpers::getAPIProperty("$api\\$apiToTest\\$classToTest", $property);

if ($amazonCurlTest) {

    Helpers::testAPI($objectToNewUp, $objectParameters, $print, $testPerformance, $iterations);

} else {

    Helpers::test($objectToNewUp, $objectParameters, $print, $testPerformance, $iterations);

}

Helpers::endClock($start);