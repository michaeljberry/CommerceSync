<?php

namespace Reverb;

use ecommerce\EcommerceInterface;
use models\channels\Channel;

class ReverbClient implements EcommerceInterface
{

    use ReverbClientCurl;

    private static $reverbInfo;
    private static $reverbAuth;
    private static $reverbStoreID;
    private static $apiTable = 'api_reverb';
    private static $channel = 'Reverb';
    protected static $instance = null;

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function instance($userID)
    {
        if (self::$instance === null) {
            self::$instance = new ReverbClient($userID);
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
        $columns = [
            'reverb_email',
            'reverb_pass',
            'reverb_auth_token',
            'store_id'
        ];

        self::$reverbInfo = Channel::getAppInfo($user_id, ReverbClient::$apiTable, ReverbClient::$channel, $columns);
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

    public static function getStoreId()
    {
        return self::$reverbStoreID;
    }

}
