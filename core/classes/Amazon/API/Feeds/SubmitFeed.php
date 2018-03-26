<?php

namespace Amazon\API\Feeds;

class SubmitFeed extends Feeds
{

    protected static $requestQuota = 15;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "minute";
    protected static $hourlyRequestQuota = 30;
    protected static $method = "POST";
    protected static $feedType;
    protected static $feedContent;
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_SubmitFeed.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "ContentMD5Value",
        "FeedContent" => [
            "required"
        ],
        "FeedType" => [
            "required",
            "format" => "FeedType"
        ],
        "MarketplaceIdList",
        "PurgeAndReplace",
        "SellerId" => [
            "required"
        ]
    ];

}