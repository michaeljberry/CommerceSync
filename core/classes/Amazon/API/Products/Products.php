<?php

namespace Amazon\API\Products;

use Amazon\API \{
    APIMethods, APIParameters, APIParameterValidation, APIProperties
};

class Products
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Products";
    protected static $versionDate = "2011-10-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/products/Products_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/products/Products_ClientLibraries.html";
    protected static $dataTypes = [
        "AvailabilityType" => [
            "validWith" => [
                "NOW",
                "FUTURE_WITHOUT_DATE",
                "FUTURE_WITH_DATE"
            ]
        ],
        "BuyBoxPrice" => [
            "condition" => [
                "required"
            ],
            "LandedPrice" => [
                "format" => "MoneyType",
                "required"
            ],
            "ListingPrice" => [
                "format" => "MoneyType",
                "required"
            ],
            "Shipping" => [
                "format" => "MoneyType",
                "required"
            ],
            "Points" => [
                "format" => "Points"
            ]
        ],
        "DetailedShippingTimeType" => [
            "minimumHours",
            "maximumHours",
            "availableDate" => [
                "format" => "date"
            ],
            "availabilityType" => [
                "format" => "AvailabilityType"
            ]
        ],
        "FeeDetail" => [
            "FeeType" => [
                "format" => "FeeType",
                "required"
            ],
            "FeeAmount" => [
                "format" => "MoneyType",
                "required"
            ],
            "FeePromotion" => [
                "format" => "MoneyType"
            ],
            "TaxAmount" => [
                "format" => "MoneyType"
            ],
            "FinalFee" => [
                "format" => "MoneyType",
                "required"
            ],
            "IncludedFeeDetailList" => [
                "format" => "FeeDetail"
            ]
        ],
        "FeesEstimate" => [
            "TotalFeesEstimate" => [
                "format" => "MoneyType",
                "required"
            ],
            "FeeDetailList" => [
                "format" => "FeeDetail",
                "required"
            ]
        ],
        "FeesEstimateIdentifier" => [
            "MarketplaceId" => [
                "format" => "MarketplaceType",
                "required"
            ],
            "IdType" => [
                "required",
                "validWith" => [
                    "ASIN",
                    "SellerSKU"
                ]
            ],
            "IdValue" => [
                "required"
            ],
            "PriceToEstimateFees" => [
                "format" => "PriceToEstimateFees",
                "required"
            ],
            "IsAmazonFulfilled" => [
                "required"
            ],
            "SellerInputIdentifier" => [
                "required"
            ],
            "TimeOfFeesEstimation" => [
                "required"
            ]
        ],
        "FeesEstimateRequest" => [
            "MarketplaceId" => [
                "format" => "MarketplaceType",
                "required"
            ],
            "IdType" => [
                "required",
                "valueWith" => [
                    "ASIN",
                    "SellerSKU"
                ]
            ],
            "IdValue" => [
                "required"
            ],
            "PriceToEstimateFees" => [
                "format" => "PriceToEstimateFees",
                "required"
            ],
            "Identifier" => [
                "required"
            ],
            "IsAmazonFulfilled" => [
                "required"
            ]
        ],
        "FeesEstimateResult" => [
            "FeesEstimateIdentifier" => [
                "format" => "FeesEstimateIdentifier",
                "required"
            ],
            "FeesEstimate" => [
                "format" => "FeesEstimate"
            ],
            "Status" => [
                "required",
                "validWith" => [
                    "Success",
                    "ClientError",
                    "ServiceError"
                ]
            ],
            "Error"
        ],
        "FeeType" => [
            "validWith" => [
                "ReferralFee",
                "VariableClosingFee",
                "PerItemFee",
                "FBAFees",
                "FBAPickAndPack",
                "FBAWeightHandling",
                "FBAOrderHandling",
                "FBADeliveryServicesFee"
            ]
        ],
        "FulfillmentChannelType" => [
            "validWith" => [
                "Amazon",
                "Merchant"
            ]
        ],
        "LowestPrice" => [
            "condition" => [
                "required"
            ],
            "fulfillmentChannel" => [
                "format" => "FulfillmentChannelType",
                "required"
            ],
            "LandedPrice" => [
                "format" => "MoneyType",
                "required"
            ],
            "ListingPrice" => [
                "format" => "MoneyType",
                "required"
            ],
            "Shipping" => [
                "format" => "MoneyType",
                "required"
            ],
            "Points" => [
                "format" => "Points"
            ]
        ],
        "MarketplaceType",
        "MoneyType" => [
            "Amount" => [
                "greaterThan" => 0
            ],
            "CurrencyCode" => [
                "validWith" => [
                    "USD",
                    "EUR",
                    "GBP",
                    "RMB",
                    "INR",
                    "JPY",
                    "CAD",
                    "MXN"
                ]
            ]
        ],
        "OfferCount" => [
            "condition" => [
                "required"
            ],
            "fulfillmentChannel" => [
                "format" => "FulfillmentChannelType",
                "required"
            ]
        ],
        "OfferCountType" => [
            "OfferCount" => [
                "format" => "OfferCount"
            ]
        ],
        "Points" => [
            "PointsNumber" => [
                "required"
            ]
        ],
        "PriceToEstimateFees" => [
            "ListingPrice" => [
                "format" => "MoneyType",
                "required"
            ],
            "Shipping" => [
                "format" => "MoneyType"
            ],
            "Points" => [
                "format" => "Points"
            ]
        ],
        "SellerFeedbackRating" => [
            "SellerPositiveFeedbackRating",
            "FeedbackCount" => [
                "required"
            ]
        ],
        "ShipsFrom" => [
            "State",
            "Country"
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}