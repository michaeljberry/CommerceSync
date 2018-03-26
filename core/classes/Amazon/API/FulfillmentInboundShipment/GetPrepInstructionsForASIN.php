<?php

namespace Amazon\API\FulfillmentInboundShipment;

class GetPrepInstructionsForASIN extends FulfillmentInboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_GetPrepInstructionsForASIN.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "ASINList" => [
            "maximumCount" => 50,
            "required"
        ],
        "ShipToCountryCode" => [
            "length" => 2,
            "required"
        ],
        "SellerId" => [
            "required"
        ]
    ];

}