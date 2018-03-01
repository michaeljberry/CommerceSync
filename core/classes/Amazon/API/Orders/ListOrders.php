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
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "minute";
    protected static $action = "ListOrders";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrders.html";
    protected static $requiredParameters = [
        "MarketplaceId.Id.1",
        "SellerId",
    ];
    protected static $allowedParameters = [
        "CreatedAfter",
        "CreatedBefore",
        "LastUpdatedAfter",
        "LastUpdatedBefore",
        "OrderStatus.Status",
        "FulfillmentChannel",
        "PaymentMethod",
        "BuyerEmail",
        "SellerOrderId",
        "MaxResultsPerPage",
        "TFMShipmentStatus"
    ];

    public function __construct($orderStatus = "")
    {

        static::setShippingParameters($orderStatus);

        static::setDateParameter();

        static::setParameters();

        static::requestRules();

    }

    protected static function setShippingParameters($orderStatus)
    {

        if($orderStatus)
        {

            for($x = 1; $x <= count($orderStatus); $x++)
            {

                static::setParameterByKey("OrderStatus.Status.$x", $orderStatus[$x-1]);

            }

        }

    }

    protected static function setDateParameter()
    {

        $from = Amazon::getApiOrderDays();
        $from = $from["api_from"];
        $from .= " days";
        $createdAfter = new DateTime($from, new DateTimeZone("America/Boise"));
        $createdAfter = $createdAfter->format("Y-m-d\TH:i:s\Z");
        static::setParameterByKey("CreatedAfter", $createdAfter);

    }

    protected static function requestRules()
    {

        // static::requireParameterToBeSet("MarketplaceId");

        static::ensureDatesAreChronological("CreatedBefore", "CreatedAfter");

        static::ensureDatesAreChronological("LastUpdatedBefore", "LastUpdatedAfter");

        static::ensureOneOrTheOtherIsSet("CreatedAfter", "LastUpdatedAfter");

        static::lastUpdatedAfterExclusivityRule();

        static::ensureIntervalBetweenDates("CreatedAfter", "Timestamp", "PT2M");

        static::exclusiveBuyerEmail();

        static::exclusiveSellerOrderId();

        static::exclusiveCreatedAfter();

        static::ensureParameterIsInRange("MaxResultsPerPage", 1, 100);

        static::validOrderStatusRule();

        static::validFulfillmentChannel();

        static::validPaymentMethod();

        static::validTFMShipmentStatus();

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

        static::ensureParameterValuesAreValid("OrderStatus", $validOrderStatuses);

    }

    protected static function validFulfillmentChannel()
    {

        $validFulfillmentChannels = [
            "AFN",
            "MFN"
        ];

        static::ensureParameterValuesAreValid("FulfillmentChannel", $validFulfillmentChannels);
    }

    protected static function validPaymentMethod()
    {

        $validPaymentMethods = [
            "COD",
            "CVS",
            "Other"
        ];

        static::ensureParameterValuesAreValid("PaymentMethod", $validPaymentMethods);

    }

    protected static function validTFMShipmentStatus()
    {

        $validTFMShipmentStatuses = [
            "PendingPickUp",
            "LabelCanceled",
            "PickedUp",
            "AtDestinationFC",
            "Delivered",
            "RejectedByBuyer",
            "Undeliverable",
            "ReturnedToSeller",
            "Lost"
        ];

        static::ensureParameterValuesAreValid("TFMShipmentStatus", $validTFMShipmentStatuses);

    }

}