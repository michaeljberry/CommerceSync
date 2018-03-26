<?php

namespace Amazon\API\Feeds;

class GetFeedSubmissionCount extends Feeds
{

    protected static $requestQuota = 10;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 45;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 80;
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionCount.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "FeedProcessingStatusList" => [
            "format" => "FeedProcessingStatus"
        ],
        "FeedTypeList" => [
            "format" => "FeedType"
        ],
        "SellerId" => [
            "required"
        ],
        "SubmittedFromDate" => [
            "format" => "date"
        ],
        "SubmittedToDate" => [
            "format" => "date"
        ]
    ];

}