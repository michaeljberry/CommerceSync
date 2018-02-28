<?php

namespace Amazon\API;

use Amazon\AmazonClient;

trait APIParameters
{

    private static $curlParameters = [];

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

    public static function searchCurlParameters($parameterToCheck)
    {


        return array_filter(
            static::getCurlParameters(),
            function($k) use ($parameterToCheck){
                return strpos($k, $parameterToCheck) !== false;
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

    }

    protected static function setPurgeAndReplaceParameter()
    {

        self::setParameterByKey('PurgeAndReplace', 'false');

    }

    protected static function setMarketplaceIdParameter($key)
    {

        self::setParameterByKey($key, AmazonClient::getMarketplaceId());

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

        $body = static::getBody();

        if($body)
        {

            self::setParameterByKey("Body", $body);

        }

    }

    protected static function setVersionDateParameter()
    {

        self::setParameterByKey('Version', static::getVersionDate());

    }

    public static function setParameters($additionalConfiguration = [])
    {

        static::setAwsAccessKeyParameter();
        static::setActionParameter();

        if (in_array('Merchant', $additionalConfiguration))
            static::setMerchantIdParameter('Merchant');

        if (in_array('SellerId', $additionalConfiguration))
            static::setMerchantIdParameter('SellerId');

        if (in_array('MarketplaceId.Id.1', $additionalConfiguration))
            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        if (in_array('MarketplaceId', $additionalConfiguration))
            static::setMarketplaceIdParameter('MarketplaceId');

        if (in_array('PurgeAndReplace', $additionalConfiguration))
            static::setPurgeAndReplaceParameter();

        static::setFeedTypeParameter();
        static::setBodyParameter();

        static::setSignatureMethodParameter();
        static::setSignatureVersionParameter();
        static::setTimestampParameter();
        static::setVersionDateParameter();

    }

}