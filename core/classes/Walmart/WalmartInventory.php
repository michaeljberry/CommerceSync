<?php

namespace Walmart;

use WalmartAPI\Item as WalmartItem;

class WalmartInventory extends Walmart
{
    public function configure()
    {
        $wmitem = new WalmartItem([
            'consumerId' => WalmartClient::getConsumerKey(),
            'privateKey' => WalmartClient::getSecretKey()
        ]);
        return $wmitem;
    }

    public function getItem($sku)
    {
        $wmitem = $this->configure();
        $item = $wmitem->get([
            'sku' => $sku
        ]);
        return $item;
    }

    public function getAllItems()
    {
        $wmItems = $this->configure();
        $items = [];
        for($i = 0; $i < 21; $i+=20) {
            $items[] = $wmItems->list();
        }
        return $items;
    }

    public function updatePrice($sku, $price)
    {

    }

}
