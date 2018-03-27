<?php

namespace Amazon\API\MerchantFulfillment;

use Amazon\API \{
    APIMethods, APIParameters, APIParameterValidation, APIProperties
};

class MerchantFulfillment
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "MerchantFulfillment";
    protected static $versionDate = "2015-06-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/merch_fulfill/MerchFulfill_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/merch_fulfill/MerchFulfill_ClientLibraries.html";
    protected static $dataTypes = [
        "Address" => [
            "Name" => [
                "maximumLength" => 30,
                "required"
            ],
            "AddressLine1" => [
                "maximumLength" => 180,
                "required"
            ],
            "AddressLine2" => [
                "maximumLength" => 60
            ],
            "AddressLine3" => [
                "maximumLength" => 60
            ],
            "DistrictOrCounty" => [
                "maximumLength" => 30
            ],
            "Email" => [
                "maximumLength" => 256,
                "required"
            ],
            "City" => [
                "maximumLength" => 30,
                "required"
            ],
            "StateOrProvinceCode" => [
                "maximumLength" => 30
            ],
            "PostalCode" => [
                "maximumLength" => 30,
                "required"
            ],
            "CountryCode" => [
                "length" => 2,
                "required"
            ],
            "Phone" => [
                "maximumLength" => 30,
                "required"
            ]
        ],
        "CurrencyAmount" => [
            "CurrencyCode" => [
                "length" => 3,
                "required"
            ],
            "Amount" => [
                "required"
            ]
        ],
        "FileContents" => [
            "Contents" => [
                "required"
            ],
            "FileType" => [
                "required",
                "validWith" => [
                    "application/pdf",
                    "application/zpl",
                    "image/png"
                ]
            ],
            "Checksum" => [
                "required"
            ]
        ],
        "HazmatType" => [
            "validWith" => [
                "None",
                "LQHazmat"
            ]
        ],
        "Item" => [
            "OrderItemId" => [
                "required"
            ],
            "Quantity" => [
                "required"
            ]
        ],
        "Label" => [
            "CustomTextForLabel" => [
                "maximumLength" => 14
            ],
            "Dimensions" => [
                "format" => "LabelDimensions",
                "required"
            ],
            "FileContents" => [
                "format" => "FileContents",
                "required"
            ],
            "LabelFormat",
            "StandardIdForLabel" => [
                "validWith" => [
                    "AmazonOrderId"
                ]
            ]
        ],
        "LabelCustomization" => [
            "CustomTextForLabel" => [
                "maximumLength" => 14
            ],
            "StandardIdForLabel" => [
                "validWith" => [
                    "AmazonOrderId"
                ]
            ]
        ],
        "LabelDimensions" => [
            "Length" => [
                "greaterThan" => 0,
                "required"
            ],
            "Width" => [
                "greaterThan" => 0,
                "required"
            ],
            "Unit" => [
                "required",
                "validWith" => [
                    "inches",
                    "centimeters"
                ]
            ]
        ],
        "PackageDimensions" => [
            "Length" => [
                "greaterThan" => 0,
                "requiredIfNotSet" => "PredefinedPackageDimensions"
            ],
            "Width" => [
                "greaterThan" => 0,
                "requiredIfNotSet" => "PredefinedPackageDimensions"
            ],
            "Height" => [
                "greaterThan" => 0,
                "requiredIfNotSet" => "PredefinedPackageDimensions"
            ],
            "Unit" => [
                "requiredIfNotSet" => "PredefinedPackageDimensions",
                "validWith" => [
                    "inches",
                    "centimeters"
                ]
            ],
            "PredefinedPackageDimensions" => [
                "validWith" => [
                    "FedEx_Box_10" => "0-95.81 x 12.94 x 10.19 in",
                    "FedEx_Box_25" => "4.80 x 42.10 x 33.50 in",
                    "FedEx_Box_Extra_Large" => "1.88 x 11.00 x 10.75 in",
                    "FedEx_Box_Extra_Large" => "5.75 x 14.13 x 6.00 in",
                    "FedEx_Box_Large" => "7.50 x 12.38 x 3.00 in",
                    "FedEx_Box_Large" => "1.25 x 8.75 x 7.75 in",
                    "FedEx_Box_Medium" => "3.25 x 11.50 x 2.38 in",
                    "FedEx_Box_Medium" => "1.25 x 8.75 x 4.38 in",
                    "FedEx_Box_Small" => "2.38 x 10.88 x 1.50 in",
                    "FedEx_Box_Small" => "1.25 x 8.75 x 4.38 in",
                    "FedEx_Envelo" => "2.50 x 9.50 x 0.80 in",
                    "FedEx_Padded_P" => "1.75 x 14.75 x 2.00 in",
                    "FedEx_Pak" => "5.50 x 12.00 x 0.80 in",
                    "FedEx_Pak" => "2.75 x 10.25 x 0.80 in",
                    "FedEx_Tu" => "8.00 x 6.00 x 6.00 in",
                    "FedEx_XL_P" => "7.50 x 20.75 x 2.00 in",
                    "UPS_Box_10" => "1.00 x 33.50 x 26.50 cm",
                    "UPS_Box_25" => "8.40 x 43.30 x 35.00 cm",
                    "UPS_Express_B" => "6.00 x 31.50 x 9.50 cm",
                    "UPS_Express_Box_Lar" => "8.00 x 13.00 x 3.00 in",
                    "UPS_Express_Box_Medi" => "5.00 x 11.00 x 3.00 in",
                    "UPS_Express_Box_Sma" => "3.00 x 11.00 x 2.00 in",
                    "UPS_Express_Envelo" => "2.50 x 9.50 x 2.00 in",
                    "UPS_Express_Hard_P" => "4.75 x 11.50 x 2.00 in",
                    "UPS_Express_Legal_Envelo" => "5.00 x 9.50 x 2.00 in",
                    "UPS_Express_P" => "6.00 x 12.75 x 2.00 in",
                    "UPS_Express_Tu" => "7.00 x 19.00 x 16.50 cm",
                    "UPS_Laboratory_P" => "7.25 x 12.75 x 2.00 in",
                    "UPS_Pad_P" => "4.75 x 11.00 x 2.00 in",
                    "UPS_Pall" => "20.00 x 80.00 x 200.00 cm",
                    "USPS_Ca" => ".00 x 4.25 x 0.01 in",
                    "USPS_Fl" => "5.00 x 12.00 x 0.75 in",
                    "USPS_FlatRateCardboardEnvelo" => "2.50 x 9.50 x 4.00 in",
                    "USPS_FlatRateEnvelo" => "2.50 x 9.50 x 4.00 in",
                    "USPS_FlatRateGiftCardEnvelo" => "0.00 x 7.00 x 4.00 in",
                    "USPS_FlatRateLegalEnvelo" => "5.00 x 9.50 x 4.00 in",
                    "USPS_FlatRatePaddedEnvelo" => "2.50 x 9.50 x 4.00 in",
                    "USPS_FlatRateWindowEnvelo" => "0.00 x 5.00 x 4.00 in",
                    "USPS_LargeFlatRateBoardGameB" => "4.06 x 11.88 x 3.13 in",
                    "USPS_LargeFlatRateB" => "2.25 x 12.25 x 6.00 in",
                    "USPS_Lett" => "1.50 x 6.13 x 0.25 in",
                    "USPS_MediumFlatRateBo" => "1.25 x 8.75 x 6.00 in",
                    "USPS_MediumFlatRateBo" => "4.00 x 12.00 x 3.50 in",
                    "USPS_RegionalRateBox" => "0.13 x 7.13 x 5.00 in",
                    "USPS_RegionalRateBox" => "3.06 x 11.06 x 2.50 in",
                    "USPS_RegionalRateBox" => "6.25 x 14.50 x 3.00 in",
                    "USPS_RegionalRateBox" => "2.25 x 10.50 x 5.50 in",
                    "USPS_RegionalRateBo" => "5.00 x 12.00 x 12.00 in",
                    "USPS_SmallFlatRateB" => ".69 x 5.44 x 1.75 in",
                    "USPS_SmallFlatRateEnvelo" => "0.00 x 6.00 x 4.00 in"
                ]
            ]
        ],
        "Shipment" => [
            "ShipmentId" => [
                "required"
            ],
            "AmazonOrderId" => [
                "maximumLength" => 50,
                "required"
            ],
            "SellerOrderId" => [
                "maximumLength" => 64
            ],
            "ItemList" => [
                "format" => "Item",
                "required"
            ],
            "ShipFromAddress" => [
                "format" => "Address",
                "required"
            ],
            "ShipToAddress" => [
                "format" => "Address",
                "required"
            ],
            "PackageDimensions" => [
                "format" => "PackageDimensions",
                "required"
            ],
            "Weight" => [
                "format" => "Weight",
                "required"
            ],
            "Insurance" => [
                "format" => "CurrencyAmount",
                "required"
            ],
            "ShippingService" => [
                "format" => "ShippingService",
                "required"
            ],
            "Label" => [
                "format" => "Label",
                "required"
            ],
            "Status" => [
                "required",
                "validWith" => [
                    "Purchased",
                    "RefundPending",
                    "RefundRejected",
                    "RefundApplied"
                ]
            ],
            "TrackingId" => [
                "maximumLength" => 30
            ],
            "CreatedDate" => [
                "format" => "date",
                "required"
            ],
            "LastUpdatedDate" => [
                "format" => "date"
            ]
        ],
        "ShipmentRequestDetails" => [
            "AmazonOrderId" => [
                "maximumLength" => 50,
                "required"
            ],
            "SellerOrderId" => [
                "maximumLength" => 64
            ],
            "ItemList" => [
                "format" => "Item",
                "required"
            ],
            "ShipFromAddress" => [
                "format" => "Address",
                "required"
            ],
            "PackageDimensions" => [
                "format" => "PackageDimensions",
                "required"
            ],
            "Weight" => [
                "format" => "Weight",
                "required"
            ],
            "MustArriveByDate" => [
                "format" => "date"
            ],
            "ShipDate",
            "ShippingServiceOptions" => [
                "format" => "ShippingServiceOptions",
                "required"
            ],
            "LabelCustomization" => [
                "format" => "LabelCustomization"
            ]
        ],
        "ShippingServiceName" => [
            "required"
        ],
        "CarrierName" => [
            "required"
        ],
        "ShippingServiceId" => [
            "required"
        ],
        "ShippingServiceOfferId" => [
            "required"
        ],
        "ShipDate" => [
            "required"
        ],
        "EarliestEstimatedDeliveryDate",
        "LatestEstimatedDelieveryDate",
        "Rate" => [
            "format" => "CurrencyAmount",
            "required"
        ],
        "ShippingServiceOptions" => [
            "format" => "ShippingServiceOptions",
            "required"
        ],
        "AvailableLabelFormats" => [
            "validWith" => [
                "PNG",
                "PDF",
                "ZPL203"
            ]
        ],
        "ShippingServiceOptions" => [
            "DeliveryExperience" => [
                "required",
                "validWith" => [
                    "DeliveryConfirmationWithAdultSignature",
                    "DeliveryConfirmationWithSignature",
                    "DeliveryConfirmationWithoutSignature",
                    "NoTracking"
                ]
            ],
            "DeclaredValue" => [
                "format" => "CurrencyAmount"
            ],
            "CarrierWillPickUp" => [
                "required"
            ],
            "LabelFormat"
        ],
        "TemporarilyUnavailableCarrier" => [
            "CarrierName" => [
                "required"
            ]
        ],
        "TermsAndConditionsNotAcceptedCarrier" => [
            "CarrierName" => [
                "required"
            ]
        ],
        "Weight" => [
            "Value" => [
                "required"
            ],
            "Unit" => [
                "required",
                "validWith" => [
                    "oz",
                    "g"
                ]
            ]
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}
