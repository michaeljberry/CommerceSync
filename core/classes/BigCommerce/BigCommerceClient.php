<?php

namespace BigCommerce;

use Ecommerce\EcommerceInterface;
use models\channels\Channel;

class BigCommerceClient implements EcommerceInterface
{

    use BigCommerceClientCurl;

    private static $bigcommerceInfo;
    private static $bigcommerceStoreUrl;
    private static $bigcommerceUsername;
    private static $bigcommerceAPIKey;
    private static $bigcommerceStoreID;
    private static $apiTable = 'api_bigcommerce';
    private static $channel = 'BigCommerce';
    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($userID)
    {
        if (self::$instance === null) {
            self::$instance = new BigCommerceClient($userID);
        }
        return self::$instance;
    }

    protected function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setStoreUrl();
        self::setUsername();
        self::setAPIKey();
        self::setStoreID();

    }

    private function setInfo($user_id)
    {
        $columns = [
            'store_id',
            'store_url',
            'username',
            'api_key'
        ];

        self::$bigcommerceInfo = Channel::getAppInfo($user_id, BigCommerceClient::$apiTable, BigCommerceClient::$channel, $columns);
    }

    private function setStoreUrl()
    {
        self::$bigcommerceStoreUrl = self::$bigcommerceInfo['store_url'];
    }

    private function setUsername()
    {
        self::$bigcommerceUsername = decrypt(self::$bigcommerceInfo['username']);
    }

    private function setAPIKey()
    {
        self::$bigcommerceAPIKey = decrypt(self::$bigcommerceInfo['api_key']);
    }

    private function setStoreID()
    {
        self::$bigcommerceStoreID = self::$bigcommerceInfo['store_id'];
    }

    public static function getStoreUrl()
    {
        return self::$bigcommerceStoreUrl;
    }

    public static function getUsername()
    {
        return self::$bigcommerceUsername;
    }

    public static function getAPIKey()
    {
        return self::$bigcommerceAPIKey;
    }

    public static function getStoreId()
    {
        return self::$bigcommerceStoreID;
    }
}
