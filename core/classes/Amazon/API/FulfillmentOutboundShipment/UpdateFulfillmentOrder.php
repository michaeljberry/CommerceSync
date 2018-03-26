<?php

namespace Amazon\API\FulfillmentOutboundShipment;

class UpdateFulfillmentOrder extends FulfillmentOutboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_outbound/FBAOutbound_UpdateFulfillmentOrder.html";
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
            "maximumLength" => 40
        ],
        "DisplayableOrderDateTime" => [
            "format" => "date"
        ],
        "DisplayableOrderComment" => [
            "maximumLength" => 1000
        ],
        "ShippingSpeedCategory" => [
            "validWith" => [
                "Standard",
                "Expedited",
                "Priority"
            ]
        ],
        "DestinationAddress" => [
            "format" => "Address"
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
        "Items" => [
            "format" => "UpdateFulfillmentOrderItem"
        ],
        "SellerId" => [
            "required"
        ]
    ];

}