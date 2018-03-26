<?php

namespace Amazon\API\Finances;

class ListFinancialEvents extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 1800;
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEvents.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "AmazonOrderId" => [
            "incompatibleWith" => [
                "FinancialEventGroupId",
                "PostedAfter",
                "PostedBefore"
            ],
            "notIncremented"
        ],
        "FinancialEventGroupId" => [
            "incompatibleWith" => [
                "AmazonOrderId",
                "PostedAfter",
                "PostedBefore"
            ]
        ],
        "MaxResultsPerPage" => [
            "rangeWithin" => [
                "min" => 1,
                "max" => 100
            ]
        ],
        "PostedAfter" => [
            "earlierThan" => [
                "PostedBefore",
                "Timestamp"
            ],
            "format" => "date",
            "incompatibleWith" => [
                "AmazonOrderId",
                "FinancialEventGroupId"
            ]
        ],
        "PostedBefore" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "incompatibleWith" => [
                "AmazonOrderId",
                "FinancialEventGroupId"
            ],
            "laterThan" => "PostedAfter",
            "notFartherApartThan" => [
                "days" => 180,
                "from" => "FinancialEventGroupStartedAfter",
            ]
        ],
        "SellerId" => [
            "required"
        ]
    ];

}