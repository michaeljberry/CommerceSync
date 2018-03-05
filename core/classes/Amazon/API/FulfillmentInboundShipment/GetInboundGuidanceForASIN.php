<?php

namespace Amazon\API\FulfillmentInboundShipment;

class GetInboundGuidanceForASIN extends FulfillmentInboundShipment
{

    protected static $requestQuota = 200;
    protected static $restoreRate = 200;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "GetInboundGuidanceForASIN";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_GetInboundGuidanceForASIN.html";
    protected static $requiredParameters = [
        "SellerId",
        "MarketplaceId",
        "ASINList"
    ];
    protected static $allowedParameters = [];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}