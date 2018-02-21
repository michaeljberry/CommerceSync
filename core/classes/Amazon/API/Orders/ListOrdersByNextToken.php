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

    public function __construct($nextToken)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("NextToken", $nextToken);

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

    }

}