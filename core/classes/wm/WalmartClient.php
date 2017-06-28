<?php

namespace wm;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class WalmartClient implements EcommerceInterface
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

    public static function instance($user_id)
    {
        if (self::$instance === null) {
            self::$instance = new WalmartClient($user_id);
        }
        return self::$instance;
    }

    public function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setConsumerKey();
        self::setSecretKey();
        self::setAPIHeader();
        self::setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_walmart';
        $channel = 'Walmart';
        $columns = [
            'store_id',
            'consumer_id',
            'secret_key',
            'api_header'
        ];

        self::$walmartInfo = Channel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setConsumerKey()
    {
        self::$walmartConsumerKey = decrypt(self::$walmartInfo['consumer_id']);
    }

    private function setSecretKey()
    {
        self::$walmartSecretKey = decrypt(self::$walmartInfo['secret_key']);
    }

    private function setAPIHeader()
    {
        self::$walmartAPIHeader = self::$walmartInfo['api_header'];
    }

    private function setStoreID()
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

    public static function getStoreID()
    {
        return static::$walmartStoreID;
    }
}