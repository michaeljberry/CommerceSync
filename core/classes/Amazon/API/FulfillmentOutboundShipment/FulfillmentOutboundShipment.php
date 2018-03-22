<?php

namespace Amazon\API\Finances;

use Amazon\API \{
    APIMethods, APIParameters, APIParameterValidation, APIProperties
};

class Finances
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Finances";
    protected static $versionDate = "2015-05-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/MWS_GetServiceStatus.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/finances/Finances_ClientLibraries.html";
    protected static $dataTypes = [
        "Address" => [
            "Name" => [
                "maximumLength" => 50,
                "required"
            ],
            "Line1" => [
                "maximumLength" => 60,
                "required"
            ],
            "Line2" => [
                "maximumLength" => 60
            ],
            "Line3" => [
                "maximumLength" => 60
            ],
            "DistrictOrCounty" => [
                "maximumLength" => 150
            ],
            "City" => [
                "maximumLength" => 50,
                "requiredIf" => [
                    "notIn" => "JP"
                ]
            ],
            "StateOrProvinceCode" => [
                "maximumLength" => 150,
                "required"
            ],
            "CountryCode" => [
                "length" => 2,
                "required"
            ],
            "PostalCode" => [
                "maximumLength" => 20
            ],
            "PhoneNumber" => [
                "maximumLength" => 20
            ]
        ],
        "CODSettings" => [
            "IsCODRequired" => [
                "validIn" => [
                    "CN" => [
                        "true"
                    ],
                    "JP" => [
                        "true"
                    ]
                ]
            ],
            "CODCharge" => [
                "format" => "Currency",
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ],
            "CODChargeTax" => [
                "format" => "Currency",
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ],
            "ShippingCharge" => [
                "format" => "Currency",
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ],
            "ShippingChargeTax" => [
                "format" => "Currency",
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ]
        ],
        "CreateFulfillmnetOrderItem" => [
            "SellerSKU" => [
                "maximumLength" => 50,
                "required"
            ],
            "SellerFulfillmentOrderItemId" => [
                "maximumLength" => 50,
                "required"
            ],
            "Quantity" => [
                "required"
            ],
            "GiftMessage" => [
                "maximumLength" => 512
            ],
            "DisplayableComment" => [
                "maximumLength" => 250
            ],
            "FulfillmentNetworkSKU",
            "PerUnitDeclaredValue" => [
                "format" => "Currency",
                "requiredIf" => [
                    "destinationCountryIsNot" => "originatingCountry"
                ]
            ],
            "PerUnitPrice" => [
                "format" => "Currency",
                "requiredIfSet" => [
                    "IsCODRequired" => "true"
                ],
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ],
            "PerUnitTax" => [
                "format" => "Currency",
                "validIn" => [
                    "CN",
                    "JP"
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