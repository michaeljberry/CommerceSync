<?php

namespace Amazon\API\FulfillmentOutboundShipment;

class GetServiceStatus extends FulfillmentOutboundShipment
{

    protected static $requestQuota = 2;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 5;
    protected static $restoreRateTimePeriod = "minute";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_outbound/MWS_GetServiceStatus.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "SellerId" => [
            "requird"
        ]
    ];

}