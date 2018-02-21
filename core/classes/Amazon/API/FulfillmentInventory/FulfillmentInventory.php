<?php

namespace Amazon\API\FulfillmentInventory;

class FulfillmentInventory extends API
{

    protected static $feed = "FulfillmentInventory";
    protected static $feedType = "";

    protected static function getFeed()
    {

        return static::$feed;

    }

    protected static function getFeedType()
    {

        return static::$feedType;

    }

}