<?php

namespace Amazon\API\FulfillmentInboundShipment;

class ListInboundShipmentItems extends FulfillmentInboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_ListInboundShipmentItems.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "ShipmentId" => [
            "requiredIfNotSet" => [
                "LastUpdatedAfter",
                "LastUpdatedBefore"
            ]
        ],
        "LastUpdatedAfter" => [
            "earlierThan" => "LastUpdatedBefore",
            "format" => "date",
            "requiredIfSet" => "LastUpdatedBefore",
            "requiredIfNotSet" => "ShipmentId"
        ],
        "LastUpdatedBefore" => [
            "format" => "date",
            "laterThan" => "LastUpdatedAfter",
            "requiredIfSet" => "LastUpdatedAfter",
            "requiredIfNotSet" => "ShipmentId"
        ],
        "SellerId" => [
            "required"
        ]
    ];

}