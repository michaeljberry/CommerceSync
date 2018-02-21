<?php

namespace Amazon\API\Orders;

use \DateTime;
use \DateTimeZone;
use Amazon\Amazon;
use Amazon\API\Orders\Orders;

class ListOrders extends Orders
{

    protected static $requestQuota = 6;
    protected static $restoreRate = 1;
    protected static $quotaTimePeriod = "minute";
    protected static $action = "ListOrders";
    protected static $method = "POST";
    private static $curlParameters = [];

    public function __construct($shippedStatus = '')
    {

        static::setAdditionalParameters();

        static::setShippingParameters($shippedStatus);

        static::setDateParameter();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            'MarketplaceId.Id.1',
            'SellerId',
        ];

        static::setParams($additionalConfiguration);

    }

    protected static function setShippingParameters($shippedStatus)
    {

        if($shippedStatus){

            for($x = 1; $x <= count($shippedStatus); $x++){

                static::setParameterByKey("OrderStatus.Status.$x", $shippedStatus[$x-1]);

            }

        }

    }

    protected static function setDateParameter()
    {

        $from = Amazon::getApiOrderDays();
        $from = $from['api_from'];
        // $from = "-1";
        $from .= ' days';
        $createdAfter = new DateTime($from, new DateTimeZone('America/Boise'));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        static::setParameterByKey("CreatedAfter", $createdAfter);

    }

}