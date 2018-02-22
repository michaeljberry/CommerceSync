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
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrders.html";

    public function __construct($shippedStatus = '')
    {

        static::setAdditionalParameters();

        static::setShippingParameters($shippedStatus);

        static::setDateParameter();

        static::requestRules();

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

    protected static function requestRules()
    {

        if(null !== static::getParameterByKey("CreatedAfter") && null !== static::getParameterByKey("LastUpdatedAfter")){

            throw new Exception("CreatedAfter and LastUpdatedAFter cannot both be set. Please unset one and try again.");

        }

        if(null !== static::getParameterByKey("CreatedAfter") && null !== static::getParameterByKey("CreatedBefore")){

            $createdBefore = new DateTime(static::getParameterByKey("CreatedBefore"));
            $createdAfter = new DateTime(static::getParameterByKey("CreatedAfter"));

            if($createdAfter > $createdBefore){

                throw new Exception("CreatedBefore must be before CreatedAfter. Please correct the dates and try again.");

            }

        }

        if(null !== static::getParameterByKey("LastUpdatedAfter") && null !== static::getParameterByKey("LastUpdatedBefore")){

            $lastUpdatedBefore = new DateTime(static::getParameterByKey("LastUpdatedBefore"));
            $lastUpdatedAfter = new DateTime(static::getParameterByKey("LastUpdatedAfter"));

            if($lastUpdatedBefore > $lastUpdatedAfter){

                throw new Exception("LastUpdatedBefore must be before LastUpdatedAfter. Please correct the dates and try again.");

            }
        }

        if(null == static::getParameterByKey("MarketplaceId")){

            throw new Exception("MarketplaceId must be set to complete this request. Please correct and try again.");

        }

        if(null !== static::getParameterByKey("BuyerEmail") &&
        (
            null !== static::getParameterByKey("FulfillmentChannel") ||
            null !== static::getParameterByKey("OrderStatus") ||
            null !== static::getParameterByKey("PaymentMethod") ||
            null !== static::getParameterByKey("LastUpdatedAfter") ||
            null !== static::getParameterByKey("LastUpdatedBefore") ||
            null !== static::getParameterByKey("SellerOrderId")
        )){

            throw new Exception("BuyerEmail cannot be set at the same time as the following: FulfillmentChannel, OrderStatus, PaymentMethod, LastUpdatedAfter, LastUpdatedBefore, SellerOrderId. Please correct and try again.");

        }

        if(null !== static::getParameterByKey("SellerOrderId") &&
        (
            null !== static::getParameterByKey("FulfillmentChannel") ||
            null !== static::getParameterByKey("OrderStatus") ||
            null !== static::getParameterByKey("PaymentMethod") ||
            null !== static::getParameterByKey("LastUpdatedAfter") ||
            null !== static::getParameterByKey("LastUpdatedBefore") ||
            null !== static::getParameterByKey("BuyerEmail")
        )){

            throw new Exception("SellerOrderId cannot be set at the same time as the following: FulfillmentChannel, OrderStatus, PaymentMethod, LastUpdatedAfter, LastUpdatedBefore, BuyerEmail. Please correct and try again.");

        }

    }

}