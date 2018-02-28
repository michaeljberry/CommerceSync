<?php

namespace Amazon\API\Feeds;

use Amazon\API\Feeds\Feeds;

class GetFeedSubmissionCount extends Feeds
{

    protected static $requestQuota = 10;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 45;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 80;
    protected static $action = "GetFeedSubmissionCount";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionCount.html";
    protected static $requiredParameters = [
        "MarketplaceId",
        "SellerId"
    ];
    protected static $allowedParameters = [
        "FeedTypeList",
        "FeedProcessingStatusList",
        "SubmittedFromDate",
        "SubmittedToDate"
    ];

    public function __construct()
    {

        static::setParameters();

        static::requestRules();

    }

    protected static function requestRules()
    {

        static::ensureSetParametersAreAllowed(static::$allowedParameters);

    }

}