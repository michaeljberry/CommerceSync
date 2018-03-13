<?php

namespace Amazon\API\FulfillmentInboundShipment;

class CreateInboundShipmentPlan extends FulfillmentInboundShipment
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "CreateInboundShipmentPlan";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_CreateInboundShipmentPlan.html";
    protected static $requiredParameters = [
        "SellerId",
        "ShipFromAddress.Name",
        "ShipFromAddress.AddressLine1",
        "ShipFromAddress.City",
        "ShipFromAddress.CountryCode",
        "InboundShipmentPlanRequestItems"
    ];
    protected static $allowedParameters = [
        "ShipToCountryCode",
        "ShipToCountrySubdivisionCode",
        "ShipFromAddress.AddressLine2",
        "ShipFromAddress.DistrictOrCounty",
        "ShipFromAddress.StateOrProvinceCode",
        "ShipFromAddress.PostalCode",
        "LabelPrepPreference"
    ];
    protected static $parameters = [
        "LabelPrepPreference" => [
            "validWith" => [
                "AMAZON_LABEL_ONLY",
                "AMAZON_LABEL_PREFERRED",
                "SELLER_LABEL"
            ]
        ],
        "InboundShipmentPlanRequestItems" => [
            "required"
        ],
        "ShipFromAddress" => [
            "format" => "Address",
            "required"
        ],
        "ShipToCountryCode" => [
            "incompatibleWith" => "ShipToCountrySubdivisionCode",
            "maximumLength" => 2,
            "validWith" => [
                "CA",
                "DE",
                "ES",
                "FR",
                "GB",
                "IT",
                "MX",
                "US"
            ]
        ],
        "ShipToCountrySubdivisionCode" => [
            "incompatibleWith" => "ShipToCountryCode",
            "maximumLength" => 2
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

    protected static function requestRules()
    {

        static::ensureOneOrTheOtherIsSet("ShipToCountryCode", "ShipToCountrySubdivisionCode");

        static::validLabelPrepPreference();

    }

    protected static function validLabelPrepPreference()
    {

        $validLabelPrepPreferences = [
            "SELLER_LABEL",
            "AMAZON_LABEL_ONLY",
            "AMAZON_LABEL_PREFERED"
        ];

        static::ensureParameterValuesAreValid("LabelPrepPreference", $validLabelPrepPreferences);

    }

}