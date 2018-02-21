<?php

namespace Amazon\API;

trait APIMethods
{

    private static $signatureMethod = 'HmacSHA256';
    private static $signatureVersion = "2";

    protected static function setMethod($method)
    {

        static::$method = $method;

    }

    public static function getMethod()
    {

        return static::$method;

    }

    public static function getFeed()
    {

        return static::$feed;

    }

    public static function getFeedType()
    {

        return static::$feedType;

    }

    public static function getAction()
    {

        return static::$action;

    }

    public static function getVersionDate()
    {

        return static::$versionDate;

    }

}