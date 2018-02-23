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

    public function __construct($orderStatus = "")
    {

        static::setAdditionalParameters();

        static::setShippingParameters($orderStatus);

        static::setDateParameter();

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "MarketplaceId.Id.1",
            "SellerId",
        ];

        static::setParameters($additionalConfiguration);

    }

    protected static function setShippingParameters($orderStatus)
    {

        if($orderStatus){

            for($x = 1; $x <= count($orderStatus); $x++){

                static::setParameterByKey("OrderStatus.Status.$x", $orderStatus[$x-1]);

            }

        }

    }

    protected static function setDateParameter()
    {

        $from = Amazon::getApiOrderDays();
        $from = $from["api_from"];
        // $from = "-1";
        $from .= " days";
        $createdAfter = new DateTime($from, new DateTimeZone("America/Boise"));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        static::setParameterByKey("CreatedAfter", $createdAfter);

    }

    protected static function requestRules()
    {

        static::requireParameterToBeSet("MarketplaceId");

        static::ensureDatesAreChronological("CreatedBefore", "CreatedAfter");

        static::ensureDatesAreChronological("LastUpdatedBefore", "LastUpdatedAfter");

        static::lastUpdatedAfterExclusivityRule();

        if(null === static::getParameterByKey("CreatedAfter"))
        {

            $timestamp = new DateTime(static::getParameterByKey("Timestamp"));
            $adjustedTimestamp = $timestamp->sub(new DateInterval("PT2M"));
            $createdAfter = new DateTime(static::getParameterByKey("CreatedAfter"));

            if($createdAfter > $adjustedTimestamp)
            {

                throw new Exception("CreatedAfter must be no later than two minutes before Timestamp. Please correct and try again.");

            }

        }



        static::exclusiveBuyerEmail();

        static::exclusiveSellerOrderId();

        static::exclusiveCreatedAfter();

        static::validOrderStatusRule();

    }

    protected static function exclusiveCreatedAfter()
    {

        $restrictedParameters = [
            "LastUpdatedAfter"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("CreatedAfter", $restrictedParameters);
    }

    protected static function exclusiveBuyerEmail()
    {

        $restrictedParameters = [
            "FulfillmentChannel",
            "OrderStatus",
            "PaymentMethod",
            "LastUpdatedAfter",
            "LastUpdatedBefore",
            "SellerOrderId"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("BuyerEmail", $restrictedParameters);

    }

    protected static function lastUpdatedAfterExclusivityRule()
    {

        $restrictedParameters = [
            "BuyerEmail",
            "SellerOrderId"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("LastUpdatedAfter", $restrictedParameters);

    }

    protected static function exclusiveSellerOrderId()
    {

        $restrictedParameters = [
            "FulfillmentChannel",
            "OrderStatus",
            "PaymentMethod",
            "LastUpdatedAfter",
            "LastUpdatedBefore",
            "BuyerEmail"
        ];

        static::ensureMutuallyExclusiveParametersNotSet("SellerOrderId", $restrictedParameters);

    }

    protected static function validOrderStatusRule()
    {

        $validOrderStatuses = [
            "PendingAvailability",
            "Pending",
            "Unshipped",
            "PartiallyShipped",
            "InvoiceUnconfirmed",
            "Canceled",
            "Unfulfillable"
        ];

        static::ensureParametersAreValid('OrderStatus', $validOrderStatuses);

    }

}