<?php

namespace wm;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class WalmartClient implements EcommerceInterface
{
    private $walmartInfo;
    private $walmartStoreID;
    private $walmartConsumerKey;
    private $walmartSecretKey;
    private $walmartAPIHeader;

    public function __construct($user_id)
    {
        $this->setInfo($user_id);
        $this->setConsumerKey();
        $this->setSecretKey();
        $this->setAPIHeader();
        $this->setStoreID();
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

        $this->walmartInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    public static function instance($user_id)
    {
        if(self::instance === null){

        }
        return self::instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    private function setConsumerKey()
    {
        $this->walmartConsumerKey = decrypt($this->walmartInfo['consumer_id']);
    }

    private function setSecretKey()
    {
        $this->walmartSecretKey = decrypt($this->walmartInfo['secret_key']);
    }

    private function setAPIHeader()
    {
        $this->walmartAPIHeader = $this->walmartInfo['api_header'];
    }

    private function setStoreID()
    {
        $this->walmartStoreID = $this->walmartInfo['store_id'];
    }

    public function getConsumerKey()
    {
        return $this->walmartConsumerKey;
    }

    public function getSecretKey()
    {
        return $this->walmartSecretKey;
    }

    public function getAPIHeader()
    {
        return $this->walmartAPIHeader;
    }

    public static function getStoreID()
    {
        return static::$walmartStoreID;
    }
}