<?php

namespace Amazon\API\FulfillmentInboundShipment;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation, APIProperties};

class FulfillmentInboundShipment
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "FulfillmentInboundShipment";
    protected static $versionDate = "2010-10-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_ClientLibraries.html";
    protected static $dataTypes = [
        "Address" => [
            "Name" => [
                "maximumLength" => 50,
                "required"
            ],
            "AddressLine1" => [
                "maximumLength" => 180,
                "required"
            ],
            "AddressLine2" => [
                "maximumLength" => 60
            ],
            "City" => [
                "maximumLength" => 30,
                "required"
            ],
            "DistrictOrCounty" => [
                "maximumLength" => 25
            ],
            "StateOrProvinceCode" => [
                "maximumLength" => 2
            ],
            "CountryCode" => [
                "maximumLength" => 2,
                "required"
            ],
            "PostalCode" => [
                "maximumLength" => 30
            ]
        ],
        "AmazonPrepFeesDetails" => [
            "PrepInstruction" => [
                "format" => "PrepInstruction",
                "required"
            ],
            "FeePerUnit" => [
                "format" => "Amount",
                "required"
            ]
        ],
        "Amount" => [
            "CurrencyCode" => [
                "validWith" => [
                    "GBP",
                    "USD"
                ],
                "required"
            ],
            "Value" => [
                "required"
            ]
        ],
        "ASINInboundGuidance" => [
            "ASIN" => [
                "required"
            ],
            "InboundGuidance" => [
                "format" => "InboundGuidance",
                "required"
            ],
            "GuidanceReasonList" => [
                "format" => "GuidanceReason"
            ]
        ],
        "ASINPrepInstructions" => [
            "ASIN",
            "BarcodeInstruction" => [
                "validWith" => [
                    "RequiresFNSKULabel",
                    "MustProvideSellerSKU"
                ]
            ],
            "PrepGuidance" => [
                "validWith" => [
                    "ConsultHelpDocuments",
                    "NoAdditionalPrepRequired",
                    "SeePrepInstructionsList"
                ]
            ],
            "PrepInstructionList" => [
                "format" => "PrepInstruction"
            ]
        ],
        "BoxContentsFeeDetails" => [
            "TotalUnits",
            "FeePerUnit" => [
                "format" => "Amount"
            ],
            "TotalFee" => [
                "format" => "Amount"
            ]
        ],
        "BoxContentsSource" => [
            "validWith" => [
                "NONE",
                "FEED",
                "2D_BARCODE",
                "INTERACTIVE"
            ]
        ],
        "Contact" => [
            "Name" => [
                "maximumLength" => 50,
                "required"
            ],
            "Phone" => [
                "maximumLength" => 20,
                "required"
            ],
            "Email" => [
                "maximumLength" => 50,
                "required"
            ],
            "Fax" => [
                "maximumLength" => 20,
                "required"
            ]
        ],
        "Dimensions" => [
            "Unit" => [
                "validWith" => [
                    "centimeters",
                    "inches"
                ],
                "required"
            ],
            "Length" => [
                "greaterThan" => 0,
                "required"
            ],
            "Width" => [
                "greaterThan" => 0,
                "required"
            ],
            "Height" => [
                "greaterThan" => 0,
                "required"
            ]
        ],
        "GuidanceReason" => [
            "validWith" => [
                "SlowMovingASIN",
                "NoApplicableGuidance"
            ]
        ],
        "InboundGuidance" => [
            "validWith" => [
                "InboundNotRecommended",
                "InboundOK"
            ]
        ],
        "InboundShipmentHeader" => [
            "ShipmentName" => [
                "required"
            ],
            "ShipFromAddress" => [
                "format" => "Address",
                "required"
            ],
            "DestinationFulfillmentCenterId" => [
                "required"
            ],
            "LabelPrepPreference" => [
                "required",
                "validWith" => [
                    "AMAZON_LABEL_ONLY",
                    "AMAZON_LABEL_PREFERRED",
                    "SELLER_LABEL"
                ]
            ],
            "AreCasesRequired" => [
                "requiredIf" =>[
                    "AreCasesRequired" => "true"
                ],
                "validWith" => [
                    "false",
                    "true"
                ]
            ],
            "ShipmentStatus" => [
                "required",
                "validWith" => [
                    "WORKING",
                    "SHIPPED",
                    "CANCELLED" => [
                        "onlyIfOperationIs" => "UpdateInboundShipment"
                    ]
                ]
            ],
            "IntendedBoxContentsSource" => [
                "validIn" => [
                    "US"
                ],
                "validWith" => [
                    "NONE",
                    "FEED",
                    "2D_BARCODE"
                ]
            ]
        ],
        "InboundShipmentInfo" => [
            "ShipmentId",
            "ShipmentName",
            "ShipFromAddress" => [
                "format" => "Address",
                "required"
            ],
            "DestinationFulfillmentCenterId",
            "LabelPrepType" => [
                "validWith" => [
                    "AMAZON_LABEL",
                    "NO_LABEL",
                    "SELLER_LABEL"
                ]
            ],
            "ShipmentStatus" => [
                "validWith" => [
                    "CANCELLED",
                    "CHECKED_IN",
                    "CLOSED",
                    "DELETED",
                    "DELIVERED",
                    "ERROR",
                    "IN_TRANSIT",
                    "RECEIVING",
                    "SHIPPED",
                    "WORKING"
                ]
            ],
            "AreCasesRequired" => [
                "required",
                "validWith" => [
                    "false",
                    "true"
                ]
            ],
            "ConfirmedNeedByDate" => [
                "format" => "YYYY-MM-DD"
            ],
            "BoxContentsSource" => [
                "format" => "BoxContentsSource"
            ],
            "EstimatedBoxContentsFee" => [
                "format" => "BoxContentsFeeDetails"
            ]
        ],
        "InboundShipmentItem" => [
            "ShipmentId",
            "SellerSKU" => [
                "required"
            ],
            "FulfillmentNetworkSKU",
            "QuantityShipped" => [
                "required"
            ],
            "QuantityReceived",
            "QuantityInCase" => [
                "divisorOf" => "QuantityShipped"
            ],
            "PrepDetailsList" => [
                "format" => "PrepDetails"
            ],
            "ReleaseDate" => [
                "format" => "YYYY-MM-DD"
            ]
        ],
        "InboundShipmentPlan" => [
            "ShipmentId" => [
                "required"
            ],
            "DestinationFulfillmentCenterId" => [
                "required"
            ],
            "ShipToAddress" => [
                "format" => "Address",
                "required"
            ],
            "LabelPrepType" => [
                "validWith" => [
                    "AMAZON_LABEL",
                    "NO_LABEL",
                    "SELLER_LABEL"
                ]
            ],
            "Items" => [
                "format" => "InboundShipmentPlanItem",
                "required"
            ],
            "EstimatedBoxContentsFee" => [
                "format" => "BoxContentsFeeDetails"
            ]
        ],
        "InboundShipmentPlanItem" => [
            "SellerSKU" => [
                "required"
            ],
            "FulfillmentNetworkSKU" => [
                "required"
            ],
            "Quantity" => [
                "required"
            ],
            "PrepDetailsList" => [
                "format" => "PrepDetails"
            ]
        ],
        "InboundShipmentPlanRequestItem" => [
            "ASIN",
            "Condition" => [
                "validWith" => [
                    "Club",
                    "CollectibleAcceptable",
                    "CollectibleGood",
                    "CollectibleLikeNew",
                    "CollectiblePoor",
                    "CollectibleVeryGood",
                    "NewItem",
                    "NewOEM",
                    "NewOpenBox",
                    "NewWithWarranty",
                    "RefurbishedWithWarranty",
                    "Refurbished",
                    "UsedAcceptable",
                    "UsedGood",
                    "UsedLikeNew",
                    "UsedPoor",
                    "UsedRefurbished",
                    "UsedVeryGood",
                ]
            ],
            "PrepDetailsList" => [
                "format" => "PrepDetails",
            ],
            "Quantity" => [
                "required"
            ],
            "QuantityInCase" => [
                "divisorOf" => "Quantity"
            ],
            "SellerSKU" => [
                "maximumCount" => 200,
                "required"
            ]
        ],
        "IntendedBoxContentsSource" => [
            "validWith" => [
                "2D_BARCODE",
                "FEED",
                "NONE"
            ]
        ],
        "InvalidASIN" => [
            "ASIN",
            "ErrorReason" => [
                "validWith" => "DoesNotExist"
            ]
        ],
        "InvalidSKU" => [
            "ErrorReason" => [
                "validWith" => "DoesNotExist"
            ],
            "SellerSKU"
        ],
        "NonPartneredLtlDataInput" => [
            "CarrierName" => [
                "required",
                "validIn" => [
                    "UK" => [
                        "BUSINESS_POST",
                        "DHL_AIRWAYS_INC",
                        "DHL_UK",
                        "PARCELFORCE",
                        "DPD",
                        "TNT_LOGISTICS_CORPORATION",
                        "TNT",
                        "YODEL",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ],
                    "US" => [
                        "DHL_EXPRESS_USA_INC",
                        "FEDERAL_EXPRESS_CORP",
                        "UNITED_STATES_POSTAL_SERVICE",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ]
                ]
            ],
            "ProNumber" => [
                "lengthBetween" => [
                    "min" => 7,
                    "max" => 10
                ],
                "required"
            ]
        ],
        "NonPartneredLtlDataOutput" => [
            "CarrierName" => [
                "required",
                "validIn" => [
                    "UK" => [
                        "BUSINESS_POST",
                        "DHL_AIRWAYS_INC",
                        "DHL_UK",
                        "PARCELFORCE",
                        "DPD",
                        "TNT_LOGISTICS_CORPORATION",
                        "TNT",
                        "YODEL",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ],
                    "US" => [
                        "DHL_EXPRESS_USA_INC",
                        "FEDERAL_EXPRESS_CORP",
                        "UNITED_STATES_POSTAL_SERVICE",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ]
                ]
            ],
            "ProNumber" => [
                "lengthBetween" => [
                    "min" => 7,
                    "max" => 10
                ],
                "required"
            ]
        ],
        "NonPartneredSmallParcelDataInput" => [
            "CarrierName" => [
                "required",
                "validIn" => [
                    "UK" => [
                        "BUSINESS_POST",
                        "DHL_AIRWAYS_INC",
                        "DHL_UK",
                        "PARCELFORCE",
                        "DPD",
                        "TNT_LOGISTICS_CORPORATION",
                        "TNT",
                        "YODEL",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ],
                    "US" => [
                        "DHL_EXPRESS_USA_INC",
                        "FEDERAL_EXPRESS_CORP",
                        "UNITED_STATES_POSTAL_SERVICE",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ]
                ],
            ],
            "PackageList" => [
                "format" => "NonPartneredSmallParcelPackageInput",
                "required"
            ]
        ],
        "NonPartneredSmallParcelDataOutput" => [
            "PackageList" => [
                "format" => "NonPartneredSmallParcelPackageOutput"
            ]
        ],
        "NonPartneredSmallParcelPackageInput" => [
            "TrackingId" => [
                "maximumLength" => 30,
                "required"
            ]
        ],
        "NonPartneredSmallParcelPackageOutput" => [
            "CarrierName" => [
                "required",
                "validIn" => [
                    "UK" => [
                        "BUSINESS_POST",
                        "DHL_AIRWAYS_INC",
                        "DHL_UK",
                        "PARCELFORCE",
                        "DPD",
                        "TNT_LOGISTICS_CORPORATION",
                        "TNT",
                        "YODEL",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ],
                    "US" => [
                        "DHL_EXPRESS_USA_INC",
                        "FEDERAL_EXPRESS_CORP",
                        "UNITED_STATES_POSTAL_SERVICE",
                        "UNITED_PARCEL_SERVICE_INC",
                        "OTHER"
                    ]
                ],
            ],
            "TrackingId" => [
                "maximumLength" => 30,
                "required"
            ],
            "PackageStatus" => [
                "required",
                "validWith" => [
                    "CHECKED_IN",
                    "CLOSED",
                    "DELIVERED",
                    "IN_TRANSIT",
                    "RECEIVING",
                    "SHIPPED"
                ]
            ]
        ],
        "Pallet" => [
            "Dimensions" => [
                "format" => "Dimensions",
                "Height" => [
                    "lessThan" => 60
                ],
                "Length" => 40,
                "Width" => 48,
                "required"
            ]
        ],
        "PartneredEstimate" => [
            "Amount" => [
                "format" => "Amount"
            ],
            "ConfirmDeadline" => [
                "format" => "date"
            ],
            "VoidDeadline" => [
                "format" => "date"
            ]
        ],
        "PartneredLtlDataInput" => [
            "Contact" => [
                "format" => "Contact",
                "required"
            ],
            "BoxCount" => [
                "required"
            ],
            "SellerFreightClass" => [
                "validWith" => [
                    50,
                    55,
                    60,
                    65,
                    70,
                    77.5,
                    85,
                    92.5,
                    100,
                    110,
                    125,
                    150,
                    175,
                    200,
                    250,
                    300,
                    400,
                    500
                ]
            ],
            "FreightReadyDate" => [
                "format" => "YYYY-MM-DD",
                "required"
            ],
            "PalletList" => [
                "format" => "Pallet"
            ],
            "Weight" => [
                "format" => "Weight"
            ],
            "SellerDeclaredValue" => [
                "format" => "Amount"
            ]
        ],
        "PartneredLtlDataOutput" => [
            "Contact" => [
                "format" => "Contact",
                "required"
            ],
            "BoxCount" => [
                "required"
            ],
            "SellerFreightClass" => [
                "validWith" => [
                    50,
                    55,
                    60,
                    65,
                    70,
                    77.5,
                    85,
                    92.5,
                    100,
                    110,
                    125,
                    150,
                    175,
                    200,
                    250,
                    300,
                    400,
                    500
                ]
            ],
            "FreightReadyDate" => [
                "format" => "YYYY-MM-DD",
                "laterThan" => "PT2D",
                "required"
            ],
            "PalletList" => [
                "format" => "Pallet",
                "required"
            ],
            "TotalWeight" => [
                "format" => "Weight",
                "required"
            ],
            "SellerDeclaredValue" => [
                "format" => "Amount"
            ],
            "AmazonCalculatedValue" => [
                "format" => "Amount"
            ],
            "PreviewPickupDate" => [
                "format" => "date",
                "required"
            ],
            "PreviewDeliveryDate" => [
                "format" => "date",
                "required"
            ],
            "PreviewFreightClass" => [
                "required",
                "validWith" => [
                    50,
                    55,
                    60,
                    65,
                    70,
                    77.5,
                    85,
                    92.5,
                    100,
                    110,
                    125,
                    150,
                    175,
                    200,
                    250,
                    300,
                    400,
                    500
                ]
            ],
            "AmazonReferenceId" => [
                "required"
            ],
            "IsBillOfLadingAvailable" => [
                "required",
                "validWith" => [
                    "false",
                    "true"
                ]
            ],
            "PartneredEstimate" => [
                "format" => "PartneredEstimate"
            ],
            "CarrierName" => [
                "required",
                "validWith" => [
                    "DHL_EXPRESS_USA_INC",
                    "FEDERAL_EXPRESS_CORP",
                    "UNITED_STATES_POSTAL_SERVICE",
                    "UNITED_PARCEL_SERVICE_INC",
                    "OTHER"
                ]
            ]
        ],
        "PartneredSmallParcelDataInput" => [
            "CarrierName" => [
                "validIn" => [
                    "FR" => "UNITED_PARCEL_SERVICE_INC",
                    "IT" => "UNITED_PARCEL_SERVICE_INC",
                    "ES" => "UNITED_PARCEL_SERVICE_INC",
                    "UK" => "UNITED_PARCEL_SERVICE_INC",
                    "US" => "UNITED_PARCEL_SERVICE_INC",
                    "DE" => [
                        "DHL_STANDARD",
                        "UNITED_PARCEL_SERVICE_INC"
                    ]
                ]
            ],
            "PackageList" => [
                "format" => "PartneredSmallParcelPackageInput",
                "required"
            ]
        ],
        "PartneredSmallParcelDataOutput" => [
            "PackageList" => [
                "format" => "PartneredSmallParcelPackageOutput",
                "required"
            ],
            "PartneredEstimate" => [
                "format" => "PartneredEstimate"
            ]
        ],
        "PartneredSmallParcelPackageInput" => [
            "Dimensions" => [
                "format" => "Dimensions",
                "required"
            ],
            "Weight" => [
                "format" => "Weight",
                "required"
            ]
        ],
        "PartneredSmallParcelPackageOutput" => [
            "Dimensions" => [
                "format" => "Dimensions",
                "required"
            ],
            "Weight" => [
                "format" => "Weight",
                "required"
            ],
            "TrackingId" => [
                "maximumLength" => 30,
                "required"
            ],
            "PackageStatus" => [
                "required",
                "validWith" => [
                    "CHECKED_IN",
                    "CLOSED",
                    "DELIVERED",
                    "IN_TRANSIT",
                    "RECEIVING",
                    "SHIPPED"
                ]
            ],
            "CarrierName" => [
                "required"
            ]
        ],
        "PrepDetails" => [
            "PrepInstruction" => [
                "format" => "PrepInstruction",
                "required"
            ],
            "PrepOwner" => [
                "validWith" => [
                    "AMAZON",
                    "SELLER"
                ],
                "required"
            ]
        ],
        "PrepInstruction" => [
            "multipleValuesAllowed",
            "validWith" => [
                "BlackShrinkWrapping",
                "BubbleWrapping",
                "HangGarment",
                "Labeling",
                "Polybagging",
                "Taping"
            ]
        ],
        "SKUInboundGuidance" => [
            "SellerSKU" => [
                "required"
            ],
            "ASIN" => [
                "required"
            ],
            "InboundGuidance" => [
                "format" => "InboundGuidance",
                "required"
            ],
            "GuidanceReasonList" => [
                "format"  => "GuidanceReason"
            ]
        ],
        "SKUPrepInstructions" => [
            "SellerSKU",
            "ASIN",
            "BarcodeInstruction" => [
                "validWith" => [
                    "RequiredsFNSKULabel",
                    "CanUseOriginalBarcode"
                ]
            ],
            "PrepGuidance" => [
                "validWith" => [
                    "ConsultHelpDocuments",
                    "NoAdditionalPrepRequired",
                    "SeePrepInstructionsList"
                ]
            ],
            "PrepInstructionList" => [
                "format" => "PrepInstruction"
            ],
            "AmazonPrepFeesDetails" => [
                "format" => "AmazonPrepFeesDetails"
            ]
        ],
        "TransportContent" => [
            "TransportHeader" => [
                "format" => "TransportHeader",
                "required"
            ],
            "TransportDetails" => [
                "format" => "TransportDetailOutput",
                "required"
            ],
            "TransportResult" => [
                "format" => "TransportResult",
                "required"
            ]
        ],
        "TransportDetailInput" => [
            "PartneredSmallParcelData" => [
                "format" => "ParneredSmallParcelDataInput",
                "requiredIfNotSet" => [
                    "NonPartneredSmallParcelData",
                    "PartneredLtlData",
                    "NonPartneredLtlData"
                ]
            ],
            "NonPartneredSmallParcelData" => [
                "format" => "NonPartneredSmallParcelDataInput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "PartneredLtlData",
                    "NonPartneredLtlData"
                ]
            ],
            "PartneredLtlData" => [
                "format" => "PartneredLtlDataInput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "NonPartneredSmallParcelData",
                    "NonPartneredLtlData"
                ]
            ],
            "NonPartneredLtlData" => [
                "format" => "NonPartneredLtlDataInput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "NonPartneredSmallParcelData",
                    "PartneredLtlData"
                ]
            ]
        ],
        "TransportDetailOutput" => [
            "PartneredSmallParcelData" => [
                "format" => "ParneredSmallParcelDataOutput",
                "requiredIfNotSet" => [
                    "NonPartneredSmallParcelData",
                    "PartneredLtlData",
                    "NonPartneredLtlData"
                ]
            ],
            "NonPartneredSmallParcelData" => [
                "format" => "NonPartneredSmallParcelDataOutput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "PartneredLtlData",
                    "NonPartneredLtlData"
                ]
            ],
            "PartneredLtlData" => [
                "format" => "PartneredLtlDataOutput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "NonPartneredSmallParcelData",
                    "NonPartneredLtlData"
                ]
            ],
            "NonPartneredLtlData" => [
                "format" => "NonPartneredLtlDataOutput",
                "requiredIfNotSet" => [
                    "PartneredSmallParcelData",
                    "NonPartneredSmallParcelData",
                    "PartneredLtlData"
                ]
            ]
        ],
        "TransportDocument" => [
            "PdfDocument" => [
                "required"
            ],
            "Checksum" => [
                "required"
            ]
        ],
        "TransportHeader" => [
            "SellerId" => [
                "required"
            ],
            "ShipmentId" => [
                "required"
            ],
            "IsPartnered" => [
                "required",
                "validWith" => [
                    "false",
                    "true"
                ]
            ],
            "ShipmentType" => [
                "required",
                "validWith" => [
                    "LTL",
                    "SP"
                ]
            ]
        ],
        "TransportResult" => [
            "TransportStatus" => [
                "required",
                "validWith" => [
                    "WORKING",
                    "ERROR_ON_ESTIMATING",
                    "ESTIMATING",
                    "ESTIMATED",
                    "ERROR_ON_CONFIRMING",
                    "CONFIRMING",
                    "CONFIRMED",
                    "VOIDED",
                    "ERROR_ON_VOIDING",
                    "VOIDING"
                ]
            ]
        ],
        "Weight" => [
            "Unit" => [
                "required",
                "validWith" => [
                    "kilograms",
                    "pounds"
                ]
            ],
            "Value" => [
                "required"
            ]
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}