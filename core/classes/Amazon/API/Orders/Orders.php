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
        ],
        "BuyerTaxInfo" => [
            "CompanyLegalName",
            "TaxingRegion",
            "TaxClassification" => [
                "format" => "TaxClassification"
            ]
        ],
        "InvoiceData" => [
            "InvoiceRequirement" => [
                "validWith" => [
                    "Individual",
                    "Consolidated",
                    "MustNotSend"
                ]
            ],
            "BuyerSelectedInvoiceCategory",
            "InvoiceTitle",
            "InvoiceInformation" => [
                "NotApplicable",
                "BuyerSelectedInvoiceCategory",
                "ProductTitle"
            ]
        ],
        "Money" => [
            "CurrencyCode" => [
                "maximumLength" => 3
            ],
            "Amount"
        ],
        "Order" => [
            "AmazonOrderId" => [
                "required"
            ],
            "SellerOrderId",
            "PurchaseDate" => [
                "required"
            ],
            "LastUpdateDate" => [
                "format" => "date",
                "required"
            ],
            "OrderStatus" => [
                "required"
            ],
            "FulfillmentChannel",
            "SalesChannel",
            "OrderChannel",
            "ShipServiceLevel",
            "ShippingAddress" => [
                "format" => "Address"
            ],
            "OrderTotal" => [
                "format" => "Money"
            ],
            "NumberOfItemsShipped",
            "NumberOfItemsUnshipped",
            "PaymentExecutionDetail" => [
                "format" => "PaymentExecutionDetailItem",
                "validIn" => [
                    "CN",
                    "JP"
                ]
            ],
            "PaymentMethod" => [
                "validIn" => [
                    "CN" => [
                        "COD"
                    ],
                    "JP" => [
                        "COD",
                        "CVS"
                    ]
                ],
                "validWith" => "Other"
            ],
            "PaymentMethodDetails" => [
                "format" => "PaymentMethodDetails"
            ],
            "IsReplacementOrder",
            "ReplacedOrderId",
            "MarketplaceId",
            "BuyerEmail",
            "BuyerName",
            "BuyerCounty",
            "BuyerTaxInfo" => [
                "format" => "BuyerTaxInfo"
            ],
            "ShipmentServiceLevelCategory" => [
                "validWith" => [
                    "Expedited",
                    "FreeEconomy",
                    "NextDay",
                    "SameDay",
                    "SecondDay",
                    "Scheduled",
                    "Standard"
                ]
            ],
            "ShippedByAmazonTFM",
            "TFMShipmentStatus" => [
                "validWith" => [
                    "PendingPickUp",
                    "LabelCanceled",
                    "PickedUp",
                    "AtDestinationFC",
                    "Delivered",
                    "RejectedByBuyer",
                    "Undeliverable",
                    "ReturnedToSeller"
                ]
            ],
            "CbaDisplayShippingLabel" => [
                "validIn" => [
                    "US" => [
                        "CBA"
                    ],
                    "UK" => [
                        "CBA"
                    ],
                    "DE" => [
                        "CBA"
                    ]
                ]
            ],
            "OrderType" => [
                "validWith" => [
                    "StandardOrder",
                    "Preorder"
                ]
            ],
            "EarliestShipDate" => [
                "format" => "date"
            ],
            "LatestShipDate" => [
                "format" => "date"
            ],
            "EarliestDeliveryDate" => [
                "format" => "date"
            ],
            "LatestDeliveryDate" => [
                "format" => "date"
            ],
            "IsBusinessOrder" => [
                "validWith" => [
                    "false",
                    "true"
                ]
            ],
            "PurchaseOrderNumber",
            "IsPrime",
            "IsPremiumOrder"
        ],
        "OrderItem" => [
            "ASIN" => [
                "required"
            ],
            "OrderItemId" => [
                "required"
            ],
            "SellerSKU",
            "BuyerCustomizedInfo" => [
                "format" => "BuyerCustomizedInfo"
            ],
            "Title",
            "QuantityOrdered" => [
                "required"
            ],
            "QuantityShipped",
            "PointsGranted" => [
                "format" => "PointsGranted"
            ],
            "ProductInfo" => [
                "format" => "ProductInfo"
            ],
            "ItemPrice" => [
                "format" => "Money"
            ],
            "ShippingPrice" => [
                "format" => "Money"
            ],
            "GiftWrapPrice" => [
                "format" => "money"
            ],
            "TaxCollection" => [
                "format" => "TaxCollection"
            ],
            "ItemTax" => [
                "format" => "Money"
            ],
            "ShippingTax" => [
                "format" => "Money"
            ],
            "GiftWrapTax" => [
                "format" => "Money"
            ],
            "ShippingDiscount" => [
                "format" => "Money"
            ],
            "PromotionDiscount" => [
                "format" => "Money"
            ],
            "PromotionIds",
            "CODFee" => [
                "format" => "Money"
            ],
            "CODFeeDiscount" => [
                "format" => "Money"
            ],
            "IsGift",
            "GiftMessageText",
            "GiftWrapLevel",
            "InvoiceData" => [
                "format" => "InvoiceData"
            ],
            "ConditionNote",
            "ConditionId" => [
                "validWith" => [
                    "New",
                    "Used",
                    "Collectible",
                    "Refurbished",
                    "Preorder",
                    "Club"
                ]
            ],
            "ConditionSubtypeId" => [
                "validWith" => [
                    "New",
                    "Mint",
                    "Very Good",
                    "Good",
                    "Acceptable",
                    "Poor",
                    "Club",
                    "OEM",
                    "Warranty",
                    "Refurbished",
                    "Warranty",
                    "Refurbished",
                    "Open Box",
                    "Any",
                    "Other"
                ]
            ],
            "ScheduledDeliveryStartDate" => [
                "format" => "date"
            ],
            "ScheduledDeliveryEndDate" => [
                "format" => "date"
            ],
            "PriceDesignation" => [
                "validWith" => [
                    "BusinessPrice"
                ]
            ]
        ],
        "PaymentExecutionDetailItem" => [
            "Payment" => [
                "format" => "Money",
                "required"
            ],
            "PaymentMethod" => [
                "required",
                "validIn" => [
                    "CN" => [
                        "COD",
                        "GC"
                    ],
                    "JP" => [
                        "COD",
                        "GC",
                        "PointsAccount"
                    ]
                ]
            ]
        ],
        "PaymentMethodDetails" => [
            "PaymentMethodDetail"
        ],
        "ProductInfo" => [
            "NumberOfItems"
        ],
        "PointsGranted" => [
            "PointsNumber",
            "PointsMonetaryValue" => [
                "format" => "Money"
            ]
        ],
        "TaxClassification" => [
            "Name" => [
                "required"
            ],
            "Value" => [
                "required"
            ]
        ],
        "TaxCollection" => [
            "Model" => [
                "required",
                "validWith" => [
                    "MarketplaceFacilitator"
                ]
            ],
            "ResponsibleParty" => [
                "required",
                "validWith" => [
                    "Amazon Services Inc."
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