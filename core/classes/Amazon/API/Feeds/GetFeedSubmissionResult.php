<?php

namespace Amazon\API\Feeds;

use Amazon\API\Feeds\Feeds;

class GetFeedSubmissionResult extends Feeds
{

    protected static $requestQuota = 15;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "minute";
    protected static $hourlyRequestQuota = 60;
    protected static $action = "GetFeedSubmissionResult";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionResult.html";

    public function __construct($feedSubmissionId)
    {

        static::setParameterByKey("FeedSubmissionId", $feedSubmissionId);

        static::setAdditionalParameters();

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