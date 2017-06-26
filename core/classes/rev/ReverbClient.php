<?php

namespace rev;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class ReverbClient implements EcommerceInterface
{

    use ReverbClientCurl;

    private static $reverbInfo;
    private static $reverbAuth;
    private static $reverbStoreID;
    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($user_id)
    {
        if(self::$instance === null){
            self::$instance = new ReverbClient($user_id);
        }
        return self::$instance;
    }

    protected function __construct($user_id)
    {
        self::setInfo($user_id);
        self::setAuthToken();
        self::setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_reverb';
        $channel = 'Reverb';
        $columns = [
            'reverb_email',
            'reverb_pass',
            'reverb_auth_token',
            'store_id'
        ];

        self::$reverbInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setAuthToken()
    {
        self::$reverbAuth = decrypt(self::$reverbInfo['reverb_auth_token']);
    }

    private function setStoreID()
    {
        self::$reverbStoreID = self::$reverbInfo['store_id'];
    }

    public static function getAuthToken()
    {
        return self::$reverbAuth;
    }

    public static function getStoreID()
    {
        return self::$reverbStoreID;
    }

}