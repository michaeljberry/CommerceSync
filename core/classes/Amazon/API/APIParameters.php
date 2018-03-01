<?php

namespace Amazon\API;

use Amazon\AmazonClient;
use Ecommerce\Ecommerce;
use DateTime;
use DateTimeZone;

trait APIParameters
{

    private static $curlParameters = [];
    private static $orderNumberFormat = '^[0-9]{3}\-[0-9]{7}\-[0-9]{7}$';
    private static $marketplaces = [
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
    private static $incrementors = [
        "AmazonOrderId" => "Id",
        "FeedProcessingStatusList" => "Status",
        "FeedSubmissionIdList" => "Id",
        "FeedTypeList" => "Type",
        "FulfillmentChannel" => "Channel",
        "MarketplaceId" => "Id",
        "MarketplaceIdList" => "Id",
        "OrderStatus" => "Status",
        "PaymentMethod" => "Method",
        "SellerSKUList" => "Id",
        "SellerSkus" => "member"
    ];
    private static $requiredParameters = [
        "AWSAccessKeyId",
        "Action",
        // "MWSAuthToken", //For Developer Access
        "SignatureMethod",
        "SignatureVersion",
        "Timestamp",
        "Version"
    ];

    protected static function setRequiredParameter($parameter)
    {

        static::$requiredParameters[] = $parameter;

    }

    protected static function getRequiredParameters($parent = null)
    {

        if (!$parent) {
            return static::$requiredParameters;
        }

        return self::$requiredParameters;

    }

    public static function getOrderNumberFormat()
    {

        return self::$orderNumberFormat;

    }

    public static function getAllowedParameters()
    {

        return static::$allowedParameters;

    }

    public static function setParameterByKey($key, $value)
    {

        if($value)
        {

            self::$curlParameters[$key] = $value;

        }

    }

    public static function getParameterByKey($key)
    {

        return self::$curlParameters[$key] ?? null;

    }

    protected static function resetCurlParameters()
    {

        self::$curlParameters = [];

    }

    public static function getCurlParameters()
    {

        return self::$curlParameters;

    }

    public static function searchCurlParameters($parameterToCheck, $allowedParameters = null)
    {

        if(!$allowedParameters)
        {
            $allowedParameters = static::getCurlParameters();
        }

        return array_filter(
            $allowedParameters,
            function($k) use ($parameterToCheck){
                return strpos($parameterToCheck, $k) !== false;
            },
            ARRAY_FILTER_USE_KEY
        );

    }

    private static function getSignatureMethod()
    {

        return self::$signatureMethod;

    }

    private static function getSignatureVersion()
    {

        return self::$signatureVersion;

    }

    public static function setSignatureMethodParameter()
    {

        self::setParameterByKey('SignatureMethod', self::getSignatureMethod());

    }

    public static function setSignatureVersionParameter()
    {

        self::setParameterByKey('SignatureVersion', self::getSignatureVersion());

    }

    protected static function setTimestampParameter()
    {

        self::setParameterByKey('Timestamp', gmdate("Y-m-d\TH:i:s\Z", time()));

    }

    protected static function setAwsAccessKeyParameter()
    {

        self::setParameterByKey('AWSAccessKeyId', AmazonClient::getAwsAccessKey());

    }

    protected static function setActionParameter()
    {

        self::setParameterByKey('Action', static::getAction());

    }

    protected static function setMerchantIdParameter($key)
    {

        self::setParameterByKey($key, AmazonClient::getMerchantId());
        self::setRequiredParameter($key);

    }

    protected static function setPurgeAndReplaceParameter()
    {

        self::setParameterByKey('PurgeAndReplace', 'false');
        self::setRequiredParameter('PurgeAndReplace');

    }

    protected static function setMarketplaceIdParameter($key)
    {

        self::setParameterByKey($key, AmazonClient::getMarketplaceId());
        self::setRequiredParameter($key);

    }

    protected static function setFeedTypeParameter()
    {

        $feedType = static::getFeedType();

        if ($feedType)
        {

            self::setParameterByKey('FeedType', $feedType);

        }

    }

    protected static function setFeedContentParameter()
    {

        $feedContent = static::getFeedContent();

        if($feedContent)
        {

            self::setParameterByKey("FeedContent", $feedContent);

        }

    }

    protected static function setDateParameter($parameter, $date, $format = null)
    {

        $newDate = new DateTime($date, new DateTimeZone("America/Boise"));

        if(!$format)
        {

            $format = "Y-m-d\TH:i:s";

        }

        static::setParameterByKey($parameter, $newDate->format($format));

    }

    protected static function setVersionDateParameter()
    {

        self::setParameterByKey('Version', static::getVersionDate());

    }

    protected static function combineRequiredParameters()
    {

        $parentRequiredParameters = static::getRequiredParameters(true);
        $requiredParameters = static::getRequiredParameters();

        foreach($parentRequiredParameters as $parameter)
        {

            static::setRequiredParameter($parameter);

        }

    }

    protected static function combineRequiredAndAllowedParameters()
    {

        static::$allowedParameters = array_merge(
            static::getRequiredParameters(),
            static::getAllowedParameters()
        );

    }

    public static function setParameters()
    {

        static::resetCurlParameters();
        static::combineRequiredParameters();
        static::combineRequiredAndAllowedParameters();
        static::setAwsAccessKeyParameter();
        static::setActionParameter();

        if (in_array('Merchant', static::getRequiredParameters()))
            static::setMerchantIdParameter('Merchant');

        if (in_array('SellerId', static::getRequiredParameters()))
            static::setMerchantIdParameter('SellerId');

        if (in_array('MarketplaceId.Id.1', static::getRequiredParameters()))
            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        if (in_array('MarketplaceId', static::getRequiredParameters()))
            static::setMarketplaceIdParameter('MarketplaceId');

        if (in_array('PurgeAndReplace', static::getRequiredParameters()))
            static::setPurgeAndReplaceParameter();

        static::setFeedTypeParameter();
        static::setFeedContentParameter();

        static::setSignatureMethodParameter();
        static::setSignatureVersionParameter();
        static::setTimestampParameter();
        static::setVersionDateParameter();

    }

    public static function verifyParameters()
    {

        static::ensureRequiredParametersAreSet();
        static::ensureSetParametersAreAllowed();

        static::ensureParameterIsInFormat("AmazonOrderId", static::getOrderNumberFormat());

        if(method_exists(get_called_class(), "requestRules"))
        {

            static::requestRules();

        }

    }

}