<?php

namespace Amazon\API;

use Amazon\AmazonClient;

class API
{

    public function __construct()
    {

        AmazonClient::resetCurlParameters();

    }

    private static $signatureMethod = "HmacSHA256";
    private static $signatureVersion = "2";
    private static $curlParameters = [];

    public static function setParameterByKey($key, $value)
    {

        self::$curlParameters[$key] = $value;

    }

    public static function getParameterByKey($key)
    {

        return self::$curlParameters[$key];

    }

    protected static function resetCurlParameters()
    {
        self::$curlParameters = [];
    }

    public static function getCurlParameters()
    {

        return self::$curlParameters;

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

    protected static function setActionParameter($action)
    {

        self::setParameterByKey('Action', $action);

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

    protected static function setFeedTypeParameter($feedtype)
    {

        if ($feedtype) {

            self::setParameterByKey('FeedType', $feedtype);

        }

    }

    protected static function setVersionDateParameter($feed)
    {

        self::setParameterByKey('Version', AmazonClient::getAPIFeedInfo($feed)['versionDate']);

    }

    public static function setParams($action, $feedtype, $feed, $paramAdditionalConfig = [])
    {

        static::setAwsAccessKeyParameter();
        static::setActionParameter($action);

        if (in_array('Merchant', $paramAdditionalConfig))
            static::setMerchantIdParameter('Merchant');

        if (in_array('SellerId', $paramAdditionalConfig))
            static::setMerchantIdParameter('SellerId');

        if (in_array('MarketplaceId.Id.1', $paramAdditionalConfig))
            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        if (in_array('MarketplaceId', $paramAdditionalConfig))
            static::setMarketplaceIdParameter('MarketplaceId');

        if (in_array('PurgeAndReplace', $paramAdditionalConfig))
            static::setPurgeAndReplaceParameter();

        static::setFeedTypeParameter($feedtype);

        static::setSignatureMethodParameter();
        static::setSignatureVersionParameter();
        static::setTimestampParameter();
        static::setVersionDateParameter($feed);

    }

}