<?php

namespace Amazon\API;

trait APIProperties
{

    protected static $orderNumberFormat = "/^[0-9]{3}\-[0-9]{7}\-[0-9]{7}$/";
    protected static $country = "US";
    protected static $marketplaces = [
        "US" => [
            "endpoint" => "https://mws.amazonservices.com",
            "MarketplaceId" => "ATVPDKIKX0DER",
            "countrycode" => "US"
        ],
        "Canada" => [
            "endpoint" => "https://mws.amazonservices.com",
            "MarketplaceId" => "A2EUQ1WTGCTBG2",
            "countrycode" => "CA"
        ],
        "Mexico" => [
            "endpoint" => "https://mws.amazonservices.com",
            "MarketplaceId" => "A1AM78C64UM0Y8",
            "countrycode" => "MX"
        ],
        "Spain" => [
            "endpoint" => "https://mws-eu.amazonservices.com",
            "MarketplaceId" => "A1RKKUPIHCS9HS",
            "countrycode" => "ES"
        ],
        "UK" => [
            "endpoint" => "https://mws-eu.amazonservices.com",
            "MarketplaceId" => "A1F83G8C2ARO7P",
            "countrycode" => "UK"
        ],
        "France" => [
            "endpoint" => "https://mws-eu.amazonservices.com",
            "MarketplaceId" => "A13V1IB3VIYZZH",
            "countrycode" => "FR"
        ],
        "Germany" => [
            "endpoint" => "https://mws-eu.amazonservices.com",
            "MarketplaceId" => "A1PA6795UKMFR9",
            "countrycode" => "DE"
        ],
        "Italy" => [
            "endpoint" => "https://mws-eu.amazonservices.com",
            "MarketplaceId" => "APJ6JRA9NG5V4",
            "countrycode" => "IT"
        ],
        "Brazil" => [
            "endpoint" => "https://mws.amazonservices.com",
            "MarketplaceId" => "A2Q3Y263D00KWC",
            "countrycode" => "BR"
        ],
        "India" => [
            "endpoint" => "https://mws.amazonservices.in",
            "MarketplaceId" => "A21TJRUUN4KGV",
            "countrycode" => "IN"
        ],
        "China" => [
            "endpoint" => "https://mws.amazonservices.com.cn",
            "MarketplaceId" => "AAHKV2X7AFYLW",
            "countrycode" => "CN"
        ],
        "Japan" => [
            "endpoint" => "https://mws.amazonservices.jp",
            "MarketplaceId" => "A1VC38T7YXB528",
            "countrycode" => "JP"
        ],
        "Australia" => [
            "endpoint" => "https://mws.amazonservices.com.au",
            "MarketplaceId" => "A39IBJ37TRP1C6",
            "countrycode" => "AU"
        ]
    ];
    protected static $incrementors = [
        "AmazonOrderId" => "Id",
        "FeedProcessingStatusList" => "Status",
        "FeedSubmissionIdList" => "Id",
        "FeedTypeList" => "Type",
        "FulfillmentChannel" => "Channel",
        "InboundShipmentPlanRequest" => "member",
        "InboundShipmentPlanRequestItems" => "member",
        "MarketplaceId" => "Id",
        "MarketplaceIdList" => "Id",
        "OrderStatus" => "Status",
        "PaymentMethod" => "Method",
        "PrepDetailsList" => "member",
        "SellerSKUList" => "Id",
        "SellerSkus" => "member"
    ];

}