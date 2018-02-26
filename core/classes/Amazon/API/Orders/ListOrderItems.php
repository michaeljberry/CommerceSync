<?php

namespace Amazon\API\Orders;

use Amazon\API\Orders\Orders;

class ListOrderItems extends Orders
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $quotaTimePeriod = "second";
    protected static $action = "ListOrderItems";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrderItems.html";

    public function __construct($orderNumber)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("AmazonOrderId", $orderNumber);

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            'SellerId'
        ];

        static::setParameters($additionalConfiguration);

    }

    protected static function requestRules()
    {

        static::requireParameterToBeSet("AmazonOrderId");

    }

}