<?php

namespace Ebay;

use models\channels\Channel;
use Ecommerce\EcommerceInterface;
use Ecommerce\Ecommerce;

class EbayClient extends Ebay implements EcommerceInterface
{

    use EbayClientCurl;

    private static $ebayInfo;
    private static $ebayStoreID;
    private static $ebayDevID;
    private static $ebayAppID;
    private static $ebayCertID;
    private static $ebayToken;

    protected static $instance = null;

    public static function __callStatic($method, $args)
    {

        return call_user_func_array([static::instance(), $method], $args);

    }

    public static function instance()
    {

        if (static::$instance === null) {

            static::$instance = new EbayClient();

        }

        return static::$instance;

    }

    public function __construct()
    {

        static::setInfo();
        static::setDevID();
        static::setAppID();
        static::setCertID();
        static::setToken();
        static::setStoreID();

    }

    private static function setInfo()
    {

        static::$ebayInfo = Channel::getAppInfo(Ebay::getUserId(), Ebay::getApiTable(), Ebay::getChannelName(), Ebay::getApiColumns());

    }

    private static function setDevID()
    {

        static::$ebayDevID = decrypt(static::$ebayInfo['devid']);

    }

    private static function setAppID()
    {

        static::$ebayAppID = decrypt(static::$ebayInfo['appid']);

    }

    private static function setCertID()
    {

        static::$ebayCertID = decrypt(static::$ebayInfo['certid']);

    }

    private static function setToken()
    {

        static::$ebayToken = decrypt(static::$ebayInfo['token']);

    }

    private static function setStoreID()
    {

        static::$ebayStoreID = static::$ebayInfo['store_id'];

    }

    public static function getDevID()
    {

        return static::$ebayDevID;

    }

    public static function getAppID()
    {

        return static::$ebayAppID;

    }

    public static function getCertID()
    {

        return static::$ebayCertID;

    }

    public static function getToken()
    {

        return static::$ebayToken;

    }

    public static function getStoreId()
    {

        return static::$ebayStoreID;

    }

}