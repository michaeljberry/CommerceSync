<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupply extends FulfillmentInventory
{

    protected static $requestQuotaPerSecond = 30;
    protected static $restoreRatePerSecond = 2;
    protected static $responseGroup = 'Basic';
    protected static $action = __CLASS__;

    public function __construct()
    {

        $method = "POST";

        $additionalConfiguration = [
            'Merchant',
            'MarketplaceId.Id.1',
            'PurgeAndReplace',
            'MarketplaceId'
        ];

        AmazonAPI::setParams($action, static::getFeedType(), static::getFeed(), $additionalConfiguration);

        AmazonAPI::setParameterByKey('ResponseGroup', $responseGroup);

    }

}