<?php

namespace Amazon;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class AmazonClient implements EcommerceInterface
{

    use AmazonClientCurl;

    private static $amazonInfo;
    private static $amazonMerchantID;
    private static $amazonMarketplaceID;
    private static $amazonAWSAccessKey;
    private static $amazonSecretKey;
    private static $amazonStoreID;
    private static $apiTable = 'api_amazon';
    private static $channel = 'Amazon';
    protected static $instance = null;

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

    private static $columns = [
        'store_id',
        'merchantid',
        'marketplaceid',
        'aws_access_key',
        'secret_key'
    ];

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($userID)
    {
        if (self::$instance === null) {
            self::$instance = new AmazonClient($userID);
        }
        return self::$instance;
    }

    /**
     * AmazonClient constructor.
     * @param $user_id
     */
    protected function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setMerchantId();
        self::setMarketplaceId();
        self::setAwsAccessKey();
        self::setSecretKey();
        self::setStoreId();
    }

    /**
     * @param $user_id
     */
    private function setInfo($user_id)
    {
        self::$amazonInfo = Channel::getAppInfo($user_id, AmazonClient::$apiTable, AmazonClient::$channel, AmazonClient::$columns);
    }

    private function setMerchantId()
    {
        self::$amazonMerchantID = decrypt(self::$amazonInfo['merchantid']);
    }

    private function setMarketplaceId()
    {
        self::$amazonMarketplaceID = decrypt(self::$amazonInfo['marketplaceid']);
    }

    private function setAwsAccessKey()
    {
        self::$amazonAWSAccessKey = decrypt(self::$amazonInfo['aws_access_key']);
    }

    private function setSecretKey()
    {
        self::$amazonSecretKey = decrypt(self::$amazonInfo['secret_key']);
    }

    private function setStoreId()
    {
        self::$amazonStoreID = self::$amazonInfo['store_id'];
    }

    public static function getAPIFeedInfo($feed)
    {
        return self::$apiFeedInfo[$feed];
    }

    public static function getMerchantId()
    {
        return self::$amazonMerchantID;
    }

    public static function getMarketplaceId()
    {
        return self::$amazonMarketplaceID;
    }

    public static function getAwsAccessKey()
    {
        return self::$amazonAWSAccessKey;
    }

    public static function getSecretKey()
    {
        return self::$amazonSecretKey;
    }

    public static function getStoreId()
    {
        return static::$amazonStoreID;
    }

}
