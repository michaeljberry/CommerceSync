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
        $items = [];
        for($i = 0; $i < 21; $i+=20) {
            $items[] = $this->listItems($i);
        }
        return $items;
    }

    public function listItems($offset)
    {
        return $this->configure()->list([
            'offset' => $offset
        ]);
    }

    public function updatePrice($sku, $price)
    {

    }

}
