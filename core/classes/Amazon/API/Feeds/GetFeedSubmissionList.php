<?php

namespace Amazon\API\Feeds;

class GetFeedSubmissionList extends Feeds
{

    protected static $requestQuota = 10;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 45;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 80;
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionList.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "FeedProcessingStatusList" => [
            "format" => "FeedProcessingStatus"
        ],
        "FeedSubmissionIdList" => [
            "maximumCount" => 100
        ],
        "FeedTypeList" => [
            "format" => "FeedType"
        ],
        "MaxCount" => [
            "maximumCount" => 100
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