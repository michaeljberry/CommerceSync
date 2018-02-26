<?php

namespace Amazon\API\Orders;

class GetOrder extends Orders
{

    protected static $requestQuota = 6;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "minute";
    protected static $action = "GetOrder";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_GetOrder.html";

    public function __construct($amazonOrderId)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("AmazonOrderId.Id.1", $amazonOrderId);

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "SellerId"
        ];

        static::setParameters($additionalConfiguration);

    }

    protected static function requestRules()
    {

        static::requireParameterToBeSet("AmazonOrderId.Id");
    }

}