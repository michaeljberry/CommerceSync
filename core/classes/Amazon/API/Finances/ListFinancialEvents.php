<?php

namespace Amazon\API\Finances;

class ListFinancialEvents extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "ListFinancialEvents";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEvents.html";
    protected static $requiredParameters = [
        "SellerId"
    ];
    protected static $allowedParameters = [
        "MaxResultsPerPage",
        "AmazonOrderId",
        "FinancialEventGroupId",
        "PostedAfter",
        "PostedBefore"
    ];

    public function __construct()
    {

        static::setParameters();

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