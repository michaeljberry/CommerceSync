<?php

namespace Walmart;

use WalmartAPI\Item as WMItem;

class WalmartInventory extends WalmartClient
{

    public static function configure(): WMItem
    {

        return new WMItem(
            [
                'consumerId' => WalmartClient::getConsumerKey(),
                'privateKey' => WalmartClient::getSecretKey()
            ]
        );

    }

    public static function getItem($sku)
    {

        return static::configure()->get(
            [
                'sku' => $sku
            ]
        );

    }

    public static function getAllItems()
    {

        $items = [];

        for($i = 0; $i < 21; $i+=20) {

            $items[] = static::listItems($i)['MPItemView'];

        }

        return $items;

    }

    public static function listItems($offset)
    {

        return static::configure()->list(
            [
                'offset' => $offset
            ]
        );

    }

    public static function updatePrice($sku, $price)
    {

    }

}
