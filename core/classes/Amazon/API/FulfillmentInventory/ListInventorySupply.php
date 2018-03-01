<?php

namespace Amazon\API\FulfillmentInventory;

class ListInventorySupply extends FulfillmentInventory
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 2;
    protected static $restoreRateTime = 1;
    protected static $restoreRateTimePeriod = "second";
    protected static $action = "ListInventorySupply";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ListInventorySupply.html";
    protected static $requiredParameters = [
        "SellerId"
    ];
    protected static $allowedParameters = [
        "SellerSkus",
        "QueryStartDateTime",
        "ResponseGroup",
        "MarketplaceId"
    ];

    public function __construct($sku)
    {

        static::setParameters();

        static::setSkuParameters($sku);

        static::verifyParameters();

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

        static::ensureOneOrTheOtherIsSet("SellerSkus", "QueryStartDateTime");

        static::validResponseGroupRule();

    }

    protected static function validResponseGroupRule()
    {

        $validResponseGroups = [
            "Basic",
            "Detailed"
        ];

        static::ensureParameterValuesAreValid("ResponseGroup", $validResponseGroups);

    }

}