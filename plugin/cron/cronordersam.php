<?php

use Amazon\AmazonOrder;
use Ecommerce\Ecommerce;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

AmazonOrder::parseOrders(AmazonOrder::getUnshippedOrders());

// Ecommerce::ddXml(\Amazon\AmazonClient::amazonCurl(new \Amazon\API\Feeds\GetFeedSubmissionCount()));
// Ecommerce::dd(new \Amazon\API\Feeds\GetFeedSubmissionCount());

endClock($start);