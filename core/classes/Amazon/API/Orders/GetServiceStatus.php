<?php

namespace Amazon\API\Orders;

use Amazon\API\Orders\Orders;

class GetServiceStatus extends Orders
{

    protected static $requestQuota = 2;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 5;
    protected static $restoreRateTimePeriod = "minute";
    protected static $action = "GetServiceStatus";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/MWS_GetServiceStatus.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];

    public function __construct()
    {

        static::setAdditionalParameters();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "SellerId"
        ];

        static::setParameters($additionalConfiguration);

    }

}