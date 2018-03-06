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
    protected static $requestDataTypes = [
        "Address" => [
            "required" => [
                "Name" => [
                    "max" => 50
                ],
                "AddressLine1" => [
                    "max" => 180
                ],
                "City" => [
                    "max" => 30
                ],
                "CountryCode" => [
                    "max" => 2
                ]
            ],
            "allowed" => [
                "AddressLine2" => [
                    "max" => 180
                ],
                "DistrictOrCounty" => [
                    "max" => 25
                ],
                "StateOrProvinceCode" => [
                    "max" => 2
                ],
                "PostalCode" => [
                    "max" => 30
                ]
            ]
        ],
        "Amount" => [
            "required" => [
                "CurrencyCode" => [
                    "USD",
                    "GBP"
                ],
                "Value"
            ]
        ],
        "BoxContentsFeeDetails" => [
            "allowed" => [
                "TotalUnits",
                "FeePerUnit" => [
                    "Amount"
                ],
                "TotalFee" => [
                    "Amount"
                ]
            ]
        ],
        "BoxContentsSource" => [
            "NONE",
            "FEED",
            "2D_BARCODE",
            "INTERACTIVE"
        ],
        "Contact" => [
            "required" => [
                "Name" => [
                    "max" => 50
                ],
                "Phone" => [
                    "max" => 20
                ],
                "Email" => [
                    "max" => 50
                ],
                "Fax" => [
                    "max" => 20
                ]
            ]
        ],
        "Dimensions" => [
            "required" => [
                "Unit" => [
                    "inches",
                    "centimeters"
                ],
                "Length" => [
                    "gt" => 0
                ],
                "Width" => [
                    "gt" => 0
                ],
                "Height" => [
                    "gt" => 0
                ]
            ]
        ]
    ];

}