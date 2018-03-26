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

}