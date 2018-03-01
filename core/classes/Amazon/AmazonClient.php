<?php

namespace Amazon;

use models\channels\Channel;
use Ecommerce\EcommerceInterface;

class AmazonClient extends Amazon implements EcommerceInterface
{

    use AmazonClientCurl;

    private static $amazonInfo;
    private static $amazonMerchantID;
    private static $amazonMarketplaceID;
    private static $amazonAWSAccessKey;
    private static $amazonSecretKey;
    private static $amazonStoreID;
    private static $apiFeedInfo = [
        'FulfillmentInventory' => [
            'versionDate' => '2010-10-01'
        ],
        'Feeds' => [
            'versionDate' => '2009-01-01'
        ],
        'Products' => [
            'versionDate' => '2011-10-01'
        ],
        'Orders' => [
            'versionDate' => '2013-09-01'
        ],
        'doc' => [
            'versionDate' => '2009-01-01'
        ]
    ];

    protected static $instance = null;


    public static function __callStatic($method, $args)
    {

        return call_user_func_array([static::instance(), $method], $args);

    }

    public static function instance()
    {

        if (static::$instance === null) {

            static::$instance = new AmazonClient();

        }

        return static::$instance;

    }

    public function __construct()
    {

        static::setInfo();
        static::setMerchantId();
        static::setMarketplaceId();
        static::setAwsAccessKey();
        static::setSecretKey();
        static::setStoreId();

    }

    private static function setInfo()
    {

        static::$amazonInfo = Channel::getAppInfo(Amazon::getUserId(), AmazonClient::getApiTable(), AmazonClient::getChannelName(), AmazonClient::getApiColumns());

    }

    private static function setMerchantId()
    {

        static::$amazonMerchantID = decrypt(static::$amazonInfo['merchantid']);

    }

    private static function setMarketplaceId()
    {

        static::$amazonMarketplaceID = decrypt(static::$amazonInfo['marketplaceid']);

    }

    private static function setAwsAccessKey()
    {

        static::$amazonAWSAccessKey = decrypt(static::$amazonInfo['aws_access_key']);

    }

    private static function setSecretKey()
    {

        static::$amazonSecretKey = decrypt(static::$amazonInfo['secret_key']);

    }

    private static function setStoreId()
    {

        static::$amazonStoreID = static::$amazonInfo['store_id'];

    }

    public static function getAPIFeedInfo($feed)
    {

        return static::$apiFeedInfo[$feed];

    }

    public static function getMerchantId()
    {

        return static::$amazonMerchantID;

    }

    public static function getMarketplaceId()
    {

        return static::$amazonMarketplaceID;

    }

    public static function getAwsAccessKey()
    {

        return static::$amazonAWSAccessKey;

    }

    public static function getSecretKey()
    {

        return static::$amazonSecretKey;

    }

    public static function getStoreId()
    {

        return static::$amazonStoreID;

    }

}
