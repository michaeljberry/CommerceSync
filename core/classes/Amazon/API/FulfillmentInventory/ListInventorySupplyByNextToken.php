<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupplyByNextToken extends FulfillmentInventory
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRatePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ListInventorySupplyByNextToken.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "NextToken" => [
            "required"
        ],
        "SellerId" => [
            "required"
        ]
    ];

}