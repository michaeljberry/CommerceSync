<?php

namespace Amazon\API\Orders;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation, APIProperties};

class Orders
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Orders";
    protected static $versionDate = "2013-09-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/orders-2013-09-01/Orders_ClientLibraries.html";
    protected static $dataTypes = [
        "Address" => [
            "Name" => [
                "required"
            ],
            "AddressLine1",
            "AddressLine2",
            "AddressLine3",
            "City",
            "County",
            "District",
            "StateOrRegion",
            "PostalCode",
            "CountryCode" => [
                "maximumLength" => 2
            ],
            "Phone",
            "AddressType" => [
                "validWith" => [
                    "Commercial",
                    "Residential"
                ]
            ]
        ],
        "BuyerCustomizedInfo" => [
            "CustomizedURL" => [
                "required"
            ]
        ]
    ];

}