<?php

namespace Ebay;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class EbayClient extends Ebay implements EcommerceInterface
{

    use EbayClientCurl;

    private static $ebayInfo;
    private static $eBayDevID;
    private static $eBayAppID;
    private static $eBayCertID;
    private static $eBayToken;
    private static $eBayStoreID;

    protected static $instance = null;

    public static function __callStatic($method, $args)
    {

        return call_user_func_array([static::instance(), $method], $args);

    }

    public static function instance($userID)
    {

        if (static::$instance === null) {

            static::$instance = new EbayClient($userID);

        }

        return static::$instance;

    }

    public function __construct($userID)
    {

        static::setInfo($userID);
        static::setDevID();
        static::setAppID();
        static::setCertID();
        static::setToken();
        static::setStoreID();

    }

    private static function setInfo($userID)
    {

        // $columns = [
        //     'store_id',
        //     'devid',
        //     'appid',
        //     'certid',
        //     'token'
        // ];

        static::$ebayInfo = Channel::getAppInfo(Ebay::getUserId(), Ebay::getApiTable(), Ebay::getChannelName(), Ebay::getApiColumns());

    }

    private static function setDevID()
    {

        static::$eBayDevID = decrypt(static::$ebayInfo['devid']);

    }

    private static function setAppID()
    {

        static::$eBayAppID = decrypt(static::$ebayInfo['appid']);

    }

    private static function setCertID()
    {

        static::$eBayCertID = decrypt(static::$ebayInfo['certid']);

    }

    private static function setToken()
    {

        static::$eBayToken = decrypt(static::$ebayInfo['token']);

    }

    private static function setStoreID()
    {

        static::$eBayStoreID = static::$ebayInfo['store_id'];

    }

    public static function getDevID()
    {

        return static::$eBayDevID;

    }

    public static function getAppID()
    {

        return static::$eBayAppID;

    }

    public static function getCertID()
    {

        return static::$eBayCertID;

    }

    public static function getToken()
    {

        return static::$eBayToken;

    }

    public static function getStoreId()
    {

        return static::$eBayStoreID;

    }

}