<?php

namespace Amazon\API;

use Amazon\AmazonClient;
use Ecommerce\Ecommerce;

trait APIParameters
{

    private static $curlParameters = [];
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

    protected static function setBodyParameter()
    {

        $feedContent = static::getFeedContent();

        if($feedContent)
        {

            self::setParameterByKey("FeedContent", $feedContent);

        }

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
        static::setBodyParameter();

        static::setSignatureMethodParameter();
        static::setSignatureVersionParameter();
        static::setTimestampParameter();
        static::setVersionDateParameter();

    }

    public static function verifyParameters()
    {

        static::ensureRequiredParametersAreSet();
        static::ensureSetParametersAreAllowed();

        if(method_exists(get_called_class(), "requestRules"))
        {

            static::requestRules();

        }

    }

}