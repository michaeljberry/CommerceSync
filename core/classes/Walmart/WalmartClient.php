<?php

namespace Walmart;

use Ecommerce\EcommerceInterface;
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

        return call_user_func_array([static::instance(), $method], $args);

    }

    public static function instance()
    {

        if (static::$instance === null) {

            static::$instance = new WalmartClient();

        }

        return static::$instance;

    }

    public function __construct()
    {

        static::setInfo();
        static::setConsumerKey();
        static::setSecretKey();
        static::setAPIHeader();
        static::setStoreID();

    }

    private static function setInfo()
    {

        static::$walmartInfo = Channel::getAppInfo(Walmart::getUserId(), Walmart::getApiTable(), Walmart::getChannelName(), Walmart::getApiColumns());

    }

    private static function setConsumerKey()
    {

        static::$walmartConsumerKey = decrypt(static::$walmartInfo['consumer_id']);

    }

    private static function setSecretKey()
    {

        static::$walmartSecretKey = decrypt(static::$walmartInfo['secret_key']);

    }

    private static function setAPIHeader()
    {

        static::$walmartAPIHeader = static::$walmartInfo['api_header'];

    }

    private static function setStoreID()
    {

        static::$walmartStoreID = static::$walmartInfo['store_id'];

    }

    public static function getConsumerKey()
    {

        return static::$walmartConsumerKey;

    }

    public static function getSecretKey()
    {

        return static::$walmartSecretKey;

    }

    public static function getAPIHeader()
    {

        return static::$walmartAPIHeader;

    }

    public static function getStoreId()
    {

        return static::$walmartStoreID;

    }
}
