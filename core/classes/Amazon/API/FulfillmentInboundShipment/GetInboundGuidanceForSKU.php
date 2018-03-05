<?php

namespace Amazon\API\FulfillmentInboundShipment;

class GetInboundGuidanceForSKU extends FulfillmentInboundShipment
{

    protected static $requestQuota = 200;
    protected static $restoreRate = 200;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "GetInboundGuidanceForSKU";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_GetInboundGuidanceForSKU.html";
    protected static $requiredParameters = [
        "MarketplaceId",
        "SellerId",
        "SellerSKUList"
    ];
    protected static $allowedParameters = [];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}