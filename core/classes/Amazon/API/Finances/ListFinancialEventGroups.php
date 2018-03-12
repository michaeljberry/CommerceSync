<?php

namespace Amazon\API\Finances;

use Ecommerce\Ecommerce;

class ListFinancialEventGroups extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 1800;
    protected static $action = "ListFinancialEventGroups";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEventGroups.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "FinancialEventGroupStartedAfter" => [
            "earlierThan" => [
                "Timestamp",
                "FinancialEventGroupStartedBefore"
            ],
            "format" => "date",
            "required"
        ],
        "FinancialEventGroupStartedBefore" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "laterThan" => "FinancialEventGroupStartedAfter",
            "notFartherApartThan" => [
                "days" => 180,
                "from" => "FinancialEventGroupStartedAfter",
            ]
        ],
        "MaxResultsPerPage" => [
            "rangeWithin" => [
                "min" => 1,
                "max" => 100
            ]
        ],
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

        static::ensureParameterIsInRange("MaxResultsPerPage", 1, 100);

        static::ensureIntervalBetweenDates("FinancialEventGroupStartedAfter");

        static::ensureDatesAreChronological("FinancialEventGroupStartedBefore", "FinancialEventGroupStartedAfter");

        static::ensureDatesNotOutsideInterval("FinancialEventGroupStartedBefore", "FinancialEventGroupStartedAfter", 180);

    }

}