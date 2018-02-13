<?php

namespace Walmart;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class WalmartClient extends Walmart implements EcommerceInterface
{
    private static $walmartInfo;
    private static $walmartStoreID;
    private static $walmartConsumerKey;
    private static $walmartSecretKey;
    private static $walmartAPIHeader;

    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new WalmartClient();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::setInfo();
        self::setConsumerKey();
        self::setSecretKey();
        self::setAPIHeader();
        self::setStoreID();
    }

    private static function setInfo()
    {
        $columns = [
            'store_id',
            'consumer_id',
            'secret_key',
            'api_header'
        ];

        self::$walmartInfo = Channel::getAppInfo(Walmart::getUserId(), Walmart::getApiTable(), Walmart::getChannelName(), $columns);
    }

    private static function setConsumerKey()
    {
        self::$walmartConsumerKey = decrypt(self::$walmartInfo['consumer_id']);
    }

    private static function setSecretKey()
    {
        self::$walmartSecretKey = decrypt(self::$walmartInfo['secret_key']);
    }

    private static function setAPIHeader()
    {
        self::$walmartAPIHeader = self::$walmartInfo['api_header'];
    }

    private static function setStoreID()
    {
        self::$walmartStoreID = self::$walmartInfo['store_id'];
    }

    public static function getConsumerKey()
    {
        return self::$walmartConsumerKey;
    }

    public static function getSecretKey()
    {
        return self::$walmartSecretKey;
    }

    public static function getAPIHeader()
    {
        return self::$walmartAPIHeader;
    }

    public static function getStoreId()
    {
        return static::$walmartStoreID;
    }
}
