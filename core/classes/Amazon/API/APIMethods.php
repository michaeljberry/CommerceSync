<?php

namespace Amazon\API;

use Ecommerce\Ecommerce;


trait APIMethods
{

    private static $signatureMethod = 'HmacSHA256';
    private static $signatureVersion = "2";

    protected static function setMethod($method)
    {

        static::$method = $method;

    }

    protected static function setFeedType($feedType)
    {

        static::$feedType = $feedType;

    }

    protected static function setFeedContent($feedContent)
    {

        static::$feedContent = $feedContent;

    }

    public static function getCountry()
    {

        return self::$country;

    }

    public static function getEndpoint()
    {

        return self::$marketplaces[static::getCountry()]["endpoint"];

    }

    public static function getMarketplaceId()
    {

        return self::$marketplaces[static::getCountry()]["MarketplaceId"];

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

    public static function getFeedContent()
    {

        return static::$feedContent;

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