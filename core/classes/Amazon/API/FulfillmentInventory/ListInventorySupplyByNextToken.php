<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupplyByNextToken extends FulfillmentInventory
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRatePeriod = "second";
    protected static $action = "ListInventorySupplyByNextToken";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ListInventorySupplyByNextToken.html";
    protected static $requiredParameters = [
        "SellerId",
        "NextToken"
    ];
    protected static $allowedParameters = [];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}