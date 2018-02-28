<?php

namespace Amazon\API\Feeds;

use Amazon\API\Feeds\Feeds;

class GetFeedSubmissionListByNextToken extends Feeds
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 1800;
    protected static $action = "GetFeedSubmissionListByNextToken";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionListByNextToken.html";

    public function __construct($nextToken)
    {

        static::setParameterByKey("NextToken", $nextToken);

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

        static::requireParameterToBeSet("NextToken");
    }

}