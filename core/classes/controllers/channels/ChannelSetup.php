<?php

namespace controllers\channels;

trait ChannelSetup
{

    public function __construct($userID)
    {

        static::setUserId($userID);

    }

    protected static function setUserId($userID)
    {

        static::$userID = $userID;

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

}