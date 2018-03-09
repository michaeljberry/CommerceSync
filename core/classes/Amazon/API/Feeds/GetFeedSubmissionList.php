<?php

namespace Amazon\API\Feeds;

class GetFeedSubmissionList extends Feeds
{

    protected static $requestQuota = 10;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 45;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 80;
    protected static $action = "GetFeedSubmissionList";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_GetFeedSubmissionList.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "FeedProcessingStatusList",
        "FeedSubmissionIdList",
        "FeedTypeList",
        "MarketplaceId" => [
            "required"
        ],
        "MaxCount",
        "SellerId" => [
            "required"
        ],
        "SubmittedFromDate",
        "SubmittedToDate"

    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}