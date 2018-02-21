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

    public function __construct($orderNumber)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("AmazonOrderId", $orderNumber);

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            'SellerId'
        ];

        static::setParams($additionalConfiguration);

    }

}