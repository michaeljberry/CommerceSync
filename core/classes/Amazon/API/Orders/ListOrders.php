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
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ListOrders.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "BuyerEmail" => [
            "incompatibleWith" => [
                "FulfillmentChannel",
                "LastUpdatedAfter",
                "LastUpdatedBefore",
                "OrderStatus",
                "PaymentMethod",
                "SellerOrderId"
            ]
        ],
        "CreatedAfter" => [
            "earlierThan" => [
                "CreatedBefore",
                "Timestamp"
            ],
            "format" => "date",
            "incompatibleWith" => "LastUpdatedAfter",
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
            "earlierThan" => [
                "LastUpdatedBefore",
                "Timestamp"
            ],
            "format" => "date",
            "incompatibleWith" => [
                "BuyerEmail",
                "CreatedAfter",
                "SellerOrderId"
            ],
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

        static::verifyParameters();

    }

}