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
                "required"
            ]
        ],
        "InboundShipmentPlanRequestItem" => [
            "ASIN",
            "Condition" => [
                "validWith" => [
                    "NewItem",
                    "NewWithWarranty",
                    "NewOEM",
                    "NewOpenBox",
                    "UsedLikeNew",
                    "UsedVeryGood",
                    "UsedGood",
                    "UsedAcceptable",
                    "UsedPoor",
                    "UsedRefurbished",
                    "CollectibleLikeNew",
                    "CollectibleVeryGood",
                    "CollectibleGood",
                    "CollectibleAcceptable",
                    "CollectiblePoor",
                    "RefurbishedWithWarranty",
                    "Refurbished",
                    "Club"
                ]
            ],
            "PrepDetailsList" => [
                "format" => "PrepDetails",
            ],
            "Quantity" => [
                "required"
            ],
            "QuantityInCase",
            "SellerSKU" => [
                "maximumCount" => 200,
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
        ]
    ];
}