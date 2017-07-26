<?php

namespace Ebay;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class EbayClient implements EcommerceInterface
{

    use EbayClientCurl;

    private static $ebayInfo;
    private static $eBayDevID;
    private static $eBayAppID;
    private static $eBayCertID;
    private static $eBayToken;
    private static $eBayStoreID;
    private static $apiTable = 'api_ebay';
    private static $channel = 'Ebay';
    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($userID)
    {
        if (self::$instance === null) {
            self::$instance = new EbayClient($userID);
        }
        return self::$instance;
    }

    protected function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setDevID();
        self::setAppID();
        self::setCertID();
        self::setToken();
        self::setStoreID();
    }

    private static function setInfo($user_id)
    {
        $columns = [
            'store_id',
            'devid',
            'appid',
            'certid',
            'token'
        ];

        self::$ebayInfo = Channel::getAppInfo($user_id, EbayClient::$apiTable, EbayClient::$channel, $columns);
    }

    private static function setDevID()
    {
        self::$eBayDevID = decrypt(self::$ebayInfo['devid']);
    }

    private static function setAppID()
    {
        self::$eBayAppID = decrypt(self::$ebayInfo['appid']);
    }

    private static function setCertID()
    {
        self::$eBayCertID = decrypt(self::$ebayInfo['certid']);
    }

    private static function setToken()
    {
        self::$eBayToken = decrypt(self::$ebayInfo['token']);
    }

    private static function setStoreID()
    {
        self::$eBayStoreID = self::$ebayInfo['store_id'];
    }

    public static function getDevID()
    {
        return self::$eBayDevID;
    }

    public static function getAppID()
    {
        return self::$eBayAppID;
    }

    public static function getCertID()
    {
        return self::$eBayCertID;
    }

    public static function getToken()
    {
        return self::$eBayToken;
    }

    public static function getStoreId()
    {
        return static::$eBayStoreID;
    }

}
