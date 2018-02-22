<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupply extends FulfillmentInventory
{

    protected static $requestQuotaPerSecond = 30;
    protected static $restoreRatePerSecond = 2;
    protected static $quotaTimePeriod = "second";
    protected static $action = "ListInventorySupply";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ListInventorySupply.html";
    protected static $responseGroup = "Basic";

    public function __construct($sku)
    {

        static::setAdditionalParameters();

        static::setParameterByKey("ResponseGroup", static::$responseGroup);

        static::setSkuParameters($sku);

        static::requestRules();

    }

    protected static function setAdditionalParameters()
    {

        $additionalConfiguration = [
            "Merchant",
            "MarketplaceId.Id.1",
            "PurgeAndReplace",
            "MarketplaceId"
        ];

        static::setParameters($additionalConfiguration);

    }

    protected static function setSkuParameters($sku)
    {

        if (is_array($sku)) {

            for ($i = 0; $i < count($sku); $i++) {

                $n_sku = $sku[$i];
                $item = $i + 1;
                static::setParameterByKey("SellerSkus.member.$item", trim($n_sku));

            }

        } else {

            static::setParameterByKey("SellerSkus.member.1", $sku);

        }

    }

    protected static function requestRules()
    {

        if(
            null !== static::getParameterByKey("SellerSkus") &&
            null !== static::getParameterByKey("QueryStartDateTime")
        ){

            throw new Exception("SellerSkus and QueryStartDateTime cannot both be set. Please correct and try again.");

        }

        if(null !== static::getParameterByKey("ResponseGroup")){

            if(
                "Basic" !== static::getParameterByKey("ResponseGroup") ||
                "Detailed" !== static::getParameterByKey("ResponseGroup")
            ){

                throw new Exception(static::getParameterByKey("ResponseGroup") . " is not a valid ResponseGroup. Please correct and try again.");

            }

        }

    }

}