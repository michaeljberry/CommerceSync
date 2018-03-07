<?php

namespace Amazon\API\Orders;

use \DateTime;
use \DateTimeZone;
use Amazon\Amazon;
use Ecommerce\Ecommerce;

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
        // "MarketplaceId.Id.1",
        // "SellerId",
    ];
    protected static $allowedParameters = [
        // "CreatedAfter",
        // "CreatedBefore",
        // "LastUpdatedAfter",
        // "LastUpdatedBefore",
        // "OrderStatus.Status",
        // "FulfillmentChannel",
        // "PaymentMethod",
        // "BuyerEmail",
        // "SellerOrderId",
        // "MaxResultsPerPage",
        // "TFMShipmentStatus"
    ];
    //dependentOn
    //incompatibleWith
    //validWith
    //rangeWithin
    //maximumLength
    //format
    //laterThan -- if Timestamp default to interval PT2M
    //earlierThan -- if Timestamp default to interval PT2M
    //notMoreThanDaysApartFrom
    //requiredIfNotSet
    //required
    protected static $parameters = [
        "BuyerEmail" => [
            "incompatibleWith" => [
                "FulfillmentChannel",
                "OrderStatus",
                "PaymentMethod",
                "LastUpdatedAfter",
                "LastUpdatedBefore",
                "SellerOrderId"
            ]
        ],
        "CreatedAfter" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "incompatibleWith" => "LastUpdateAfter",
            "laterThan" => "CreatedBefore",
            "requiredIfNotSet" => "LastUpdatedAfter"
        ],
        "CreatedBefore" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "laterThan" => "CreatedAfter"
        ],
        "FulfillmentChannel" => [
            "validWith" => [
                "AFN",
                "MFN"
            ]
        ],
        "LastUpdatedAfter" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "incompatibleWith" => [
                "BuyerEmail",
                "CreatedAfter",
                "SellerOrderId"
            ],
            "laterThan" => "LastUpdatedBefore",
            "requiredIfNotSet" => "CreatedAfter"
        ],
        "LastUpdatedBefore" => [
            "earlierThan" => "Timestamp",
            "format" => "date",
            "laterThan" => "LastUpdatedAfter"
        ],
        "MarketplaceId" => [
            "maximumLength" => 50,
            "required"
        ],
        "MaxResultsPerPage" => [
            "rangeWithin" => [
                "min" => 1,
                "max" => 100
            ]
        ],
        "OrderStatus" => [
            "validWith" => [
                "PendingAvailability",
                "Pending",
                "Unshipped" => [
                    "dependentOn" => "PartiallyShipped"
                ],
                "PartiallyShipped" => [
                    "dependentOn" => "Unshipped"
                ],
                "InvoiceUnconfirmed",
                "Canceled",
                "Unfulfillable"
            ]
        ],
        "PaymentMethod" => [
            "validWith" => [
                "COD",
                "CVS",
                "Other"
            ]
        ],
        "SellerId" => [
            "required"
        ],
        "SellerOrderId" => [
            "incompatibleWith" => [
                "FulfillmentChannel",
                "OrderStatus",
                "PaymentMethod",
                "LastUpdatedAfter",
                "LastUpdatedBefore",
                "BuyerEmail"
            ]
        ],
        "TFMShipmentStatus" => [
            "validWith" => [
                "PendingPickUp",
                "LabelCanceled",
                "PickedUp",
                "AtDestinationFC",
                "Delivered",
                "RejectedByBuyer",
                "Undeliverable",
                "ReturnedToSeller",
                "Lost"
            ]
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        Ecommerce::dd(static::getCurlParameters());

        static::verifyParameters();

    }

    protected static function requestRules()
    {

        static::ensureDatesAreChronological("CreatedAfter", "CreatedBefore");

        static::ensureDatesAreChronological("LastUpdatedAfter", "LastUpdatedBefore");

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