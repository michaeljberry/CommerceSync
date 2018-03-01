<?php

namespace Amazon\API\Feeds;

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
    protected static $requiredParameters = [
        "MarketplaceId",
        "SellerId",
        "NextToken"
    ];
    protected static $allowedParameters = [];

    public function __construct($nextToken)
    {

        static::setParameters();

        static::setParameterByKey("NextToken", $nextToken);

        static::verifyParameters();

    }

}