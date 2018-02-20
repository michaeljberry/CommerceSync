<?php

namespace Amazon\API;

class FulfillmentInventory extends API
{

    protected static $requestQuotaPerSecond = 30;
    protected static $restoreRatePerSecond = 2;
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

    protected static function ListInventorySupply($responseGroup = 'Basic')
    {

        $action = __FUNCTION__;
        $feedType = '';
        $method = "POST";

        $additionalConfiguration = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonClient::setParams($action, static::getFeedType(), static::getFeed(), $additionalConfiguration);

        AmazonClient::setParameterByKey('ResponseGroup', $responseGroup);

    }

}