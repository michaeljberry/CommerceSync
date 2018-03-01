<?php

namespace Amazon\API\Orders;

class ListOrderItems extends Orders
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "ListOrderItems";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrderItems.html";
    protected static $requiredParameters = [
        "SellerId",
        "AmazonOrderId"
    ];
    protected static $allowedParameters = [];

    public function __construct($orderNumber)
    {

        static::setParameters();

        static::setParameterByKey("AmazonOrderId", $orderNumber);

        static::verifyParameters();

    }

}