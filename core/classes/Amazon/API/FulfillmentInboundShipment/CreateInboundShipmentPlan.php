<?php

namespace Amazon\API\FulfillmentInboundShipment;

class CreateInboundShipmentPlan extends FulfillmentInboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_CreateInboundShipmentPlan.html";
    protected static $requiredParameters = [];
    protected static $allowedParameters = [];
    protected static $parameters = [
        "ShipFromAddress" => [
            "format" => "Address",
            "required"
        ],
        "ShipToCountryCode" => [
            "incompatibleWith" => "ShipToCountrySubdivisionCode",
            "maximumLength" => 2,
            "validIn" => [
                "North America" => [
                    "CA",
                    "MX",
                    "US"
                ],
                "Europe" => [
                    "DE",
                    "ES",
                    "FR",
                    "GB",
                    "IT"
                ]
            ]
        ],
        "ShipToCountrySubdivisionCode" => [
            "incompatibleWith" => "ShipToCountryCode",
            "maximumLength" => 2
        ],
        "LabelPrepPreference" => [
            "validWith" => [
                "AMAZON_LABEL_ONLY",
                "AMAZON_LABEL_PREFERRED",
                "SELLER_LABEL"
            ]
        ],
        "InboundShipmentPlanRequestItems" => [
            "format" => "InboundShipmentPlanRequestItem",
            "required"
        ],
        "SellerId" => [
            "required"
        ]
    ];

}