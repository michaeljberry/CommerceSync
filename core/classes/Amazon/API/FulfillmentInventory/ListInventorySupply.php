<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupply extends FulfillmentInventory
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ListInventorySupply.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "SellerSkus" => [
            "incompatibleWith" => "QueryStartDateTime",
            "maximumCount" => 50,
            "requiredIfNotSet" => "QueryStartDateTime"
        ],
        "QueryStartDateTime" => [
            "format" => "date",
            "incompatibleWith" => "SellerSkus"
        ],
        "ResponseGroup" => [
            "validWith" => [
                "Basic",
                "Detailed"
            ]
        ],
        "MarketplaceId",
        "SellerId" => [
            "required"
        ]
    ];

}