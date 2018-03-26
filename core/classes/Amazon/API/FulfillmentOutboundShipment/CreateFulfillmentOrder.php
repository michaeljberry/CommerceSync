<?php

namespace Amazon\API\FulfillmentOutboundShipment;

class CreateFulfillmentOrder extends FulfillmentOutboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_outbound/FBAOutbound_CreateFulfillmentOrder.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "MarketplaceId",
        "SellerFulfillmentOrderId" => [
            "maximumLength" => 40,
            "required"
        ],
        "FulfillmentAction" => [
            "validWith" => [
                "Ship",
                "Hold"
            ]
        ],
        "DisplayableOrderId" => [
            "minimumLength" => 1,
            "maximumLength" => 40,
            "required"
        ],
        "DisplayableOrderDateTime" => [
            "format" => "date",
            "required"
        ],
        "DisplayableOrderComment" => [
            "maximumLength" => 1000,
            "required"
        ],
        "ShippingSpeedCategory" => [
            "required",
            "validWith" => [
                "Standard",
                "Expedited",
                "Priority",
                "ScheduledDelivery"
            ]
        ],
        "DestinationAddress" => [
            "format" => "Address",
            "required"
        ],
        "FulfillmentPolicy" => [
            "validWith" => [
                "FillOrKill",
                "FillAll",
                "FillAllAvailable"
            ]
        ],
        "NotificationEmailList" => [
            "maximumLength" => 64
        ],
        "CODSettings" => [
            "validIn" => [
                "CN",
                "JP"
            ]
        ],
        "Items" => [
            "format" => "CreateFulfillmentOrderItem",
            "required"
        ],
        "DeliveryWindow" => [
            "requiredIf" => [
                "ShippingSpeedCategory" => "ScheduledDelivery"
            ],
            "validIn" => [
                "JP"
            ]
        ],
        "SellerId" => [
            "required"
        ]
    ];

}