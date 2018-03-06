<?php

namespace Amazon\API\Feeds;

class SubmitFeed extends Feeds
{

    protected static $requestQuota = 15;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "minute";
    protected static $hourlyRequestQuota = 30;
    protected static $action = "SubmitFeed";
    protected static $method = "POST";
    protected static $feedType;
    protected static $feedContent;
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_SubmitFeed.html";
    protected static $requiredParameters = [
        "MarketplaceId",
        "SellerId",
        "FeedType",
        "FeedContent"
    ];
    protected static $allowedParameters = [
        "MarketetplaceIdList.Id",
        "PurgeAndReplace",
        "ContentMD5Value"
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}