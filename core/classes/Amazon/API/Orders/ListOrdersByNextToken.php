<?php

namespace Amazon\API\Orders;

use Amazon\API\Orders\Orders;

class ListOrdersByNextToken extends Orders
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $quotaTimePeriod = "second";
    protected static $action = "ListOrdersByNextToken";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrdersByNextToken.html";

    public function __construct($nextToken)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("NextToken", $nextToken);

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

    }

    protected static function requestRules()
    {

        if(null == static::getParameterByKey("NextToken")){

            throw new Exception("NextToken must be set. Please correct and try again.");

        }

    }

}