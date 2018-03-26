<?php

namespace Amazon\API\FulfillmentOutboundShipment;

class ListReturnReasonCodes extends FulfillmentOutboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "ListReturnReasonCodes";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_outbound/FBAOutbound_ListReturnReasonCodes.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "MarketplaceId",
        "SellerFulfillmentOrderId" => [
            "requiredIfNotSet" => "MarketplaceId"
        ],
        "SellerSKU" => [
            "required"
        ],
        "Language"
    ];

}