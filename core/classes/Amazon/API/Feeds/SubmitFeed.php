<?php

namespace Amazon\API\Feeds;

use Amazon\API\Feeds\Feeds;

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
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_SubmitFeed.html";

    public function __construct($feedType)
    {

        static::setFeedType($feedType);
        static::setAdditionalParameters();

    }

    private static function setFeedType($feedType)
    {

        static::$feedType = $feedType;

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "MarketplaceId",
            "SellerId"
        ];

        static::setParameters($additionalConfiguration);

    }

}