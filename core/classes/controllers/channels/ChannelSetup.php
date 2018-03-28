<?php

namespace controllers\channels;

use models\channels\Channel;
use Ecommerce\Ecommerce;

trait ChannelSetup
{

    public function __construct($userID)
    {

        static::setUserId($userID);
        static::setInfo();

    }

    protected static function setUserId($userID)
    {

        static::$userID = $userID;

    }

    private static function setInfo()
    {

        static::$channelInfo = Channel::getAppInfo(static::getUserId(), static::getApiTable(), static::getChannelName(), static::getApiColumns());
        static::setStoreId();
    }

    private static function setStoreId()
    {

        static::$storeID = static::$channelInfo['store_id'];

    }

    protected static function getUserId()
    {

        return static::$userID;

    }

    protected static function getApiTable()
    {

        return static::$apiTable;

    }

    protected static function getApiColumns()
    {

        return static::$apiColumns;

    }

    protected static function getChannelName()
    {

        return static::$channel;

    }

    public static function getStoreId()
    {

        return static::$storeID;

    }

}