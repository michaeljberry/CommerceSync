<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupply extends FulfillmentInventory
{

    protected static $requestQuotaPerSecond = 30;
    protected static $restoreRatePerSecond = 2;
    protected static $responseGroup = 'Basic';

    public function __construct()
    {

        $action = __CLASS__;
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