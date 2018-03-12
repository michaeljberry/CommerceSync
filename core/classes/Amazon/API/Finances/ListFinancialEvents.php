<?php

namespace Amazon\API\Finances;

class ListFinancialEvents extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 1800;
    protected static $action = "ListFinancialEvents";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEvents.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "AmazonOrderId",
        "FinancialEventGroupId",
        "MaxResultsPerPage",
        "PostedAfter",
        "PostedBefore",
        "SellerId" => [
            "required"
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

    protected static function requestRules()
    {

        static::exclusiveAmazonOrderId();

        static::exclusiveFinancialEventGroupId();

        static::exclusivePostedAfter();

        static::ensureParameterIsInRange("MaxResultsPerPage", 1, 100);

    }

    protected static function exclusiveAmazonOrderId()
    {

        $restrictedParameters = [
            "FinancialEventGroupId",
            "PostedAfter",
            "PostedBefore"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("AmazonOrderId", $restrictedParameters);

    }

    protected static function exclusiveFinancialEventGroupId()
    {

        $restrictedParameters = [
            "AmazonOrderId",
            "PostedAfter",
            "PostedBefore"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("FinancialEventGroupId", $restrictedParameters);

    }

    protected static function exclusivePostedAfter()
    {

        $restrictedParameters = [
            "AmazonOrderId",
            "FinancialEventGroupId",
            "PostedBefore"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("PostedAfter", $restrictedParameters);

    }

}