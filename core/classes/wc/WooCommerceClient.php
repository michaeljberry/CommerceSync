<?php

namespace wc;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class WooCommerceClient implements EcommerceInterface
{
    use WooCommerceClientCurl;

    private static $woocommerceInfo;
    private static $woocommerceConsumerKey;
    private static $woocommerceSecretKey;
    private static $woocommerceSite;
    private static $woocommerceStoreID;
    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($user_id)
    {
        if (self::$instance === null) {
            self::$instance = new WooCommerceClient($user_id);
        }
        return self::$instance;
    }

    protected function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setConsumerKey();
        self::setSecretKey();
        self::setSite();
        self::setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_woocommerce';
        $channel = 'WooCommerce';
        $columns = [
            'store_id',
            'consumer_key',
            'consumer_secret',
            'site'
        ];

        self::$woocommerceInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setConsumerKey()
    {
        self::$woocommerceConsumerKey = decrypt(self::$woocommerceInfo['consumer_key']);
    }

    private function setSecretKey()
    {
        self::$woocommerceSecretKey = decrypt(self::$woocommerceInfo['consumer_secret']);
    }

    private function setSite()
    {
        self::$woocommerceSite = self::$woocommerceInfo['site'];
    }

    private function setStoreID()
    {
        self::$woocommerceStoreID = self::$woocommerceInfo['store_id'];
    }

    public static function getConsumerKey()
    {
        return self::$woocommerceConsumerKey;
    }

    public static function getSecretKey()
    {
        return self::$woocommerceSecretKey;
    }

    public static function getSite()
    {
        return self::$woocommerceSite;
    }

    public static function getStoreID()
    {
        return self::$woocommerceStoreID;
    }
}