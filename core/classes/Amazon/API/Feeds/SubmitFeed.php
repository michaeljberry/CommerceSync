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
    protected static $body;
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_SubmitFeed.html";

    public function __construct($feedType, $body)
    {

        static::setFeedType($feedType);

        static::setBody($body);

        static::setAdditionalParameters();

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "MarketplaceId",
            "SellerId"
        ];

        static::setParameters($additionalConfiguration);

    }

    protected static function requestRules()
    {

        static::requireParameterToBeSet("FeedType");

        static::requireParameterToBeSet("Body");

    }

}