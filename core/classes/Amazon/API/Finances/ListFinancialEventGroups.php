<?php

namespace Amazon\API\Finances;

class ListFinancialEventGroups extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "ListFinancialEventGroups";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEventGroups.html";
    protected static $requiredParameters = [
        "SellerId",
        "FinancialEventGroupStartedAfter"
    ];
    protected static $allowedParameters = [
        "MaxResultsPerPage",
        "FinancialEventGroupStartedBefore"
    ];

    public function __construct()
    {

        static::setParameters();

        static::verifyParameters();

    }

    protected static function requestRules()
    {

        static::ensureParameterIsInRange("MaxResultsPerPage", 1, 100);

        static::ensureIntervalBetweenDates("FinancialEventGroupStartedAfter", "Timestamp", "PT2M");

        static::ensureDatesAreChronological("FinancialEventGroupStartedBefore", "FinancialEventGroupStartedAfter");

    }

}