<?php

namespace Amazon\API\FulfillmentInventory;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation, APIProperties};

class FulfillmentInventory
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "FulfillmentInventory";
    protected static $versionDate = "2010-10-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ClientLibraries.html";
    protected static $dataTypes = [
        "InventorySupply" => [
            "SellerSKU",
            "FNSKU" => [
                "required"
            ],
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
            "TotalSupplyQuantity" => [
                "required"
            ],
            "InStockSupplyQuantity" => [
                "required"
            ],
            "EarliestAvailability" => [
                "format" => "Timepoint"
            ],
            "SupplyDetail" => [
                "format" => "InventorySupplyDetail"
            ]
        ],
        "InventorySupplyDetail" => [
            "Quantity" => [
                "required"
            ],
            "SupplyType" => [
                "required",
                "validWith" => [
                    "InStock",
                    "Inbound",
                    "Transfer"
                ]
            ],
            "EarliestAvailableToPick" => [
                "format" => "date",
                "required"
            ],
            "LatestAvailableToPick" => [
                "format" => "date",
                "required"
            ]
        ],
        "Timepoint" => [
            "TimepointType" => [
                "required",
                "validWith" => [
                    "Immediately",
                    "DateTime",
                    "Unknown"
                ]
            ],
            "DateTime" => [
                "requiredIfSet" => [
                    "TimepointType" => "DateTime"
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