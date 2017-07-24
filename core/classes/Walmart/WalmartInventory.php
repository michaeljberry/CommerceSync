<?php

namespace Walmart;

use \WalmartAPI\Item as WalmartItem;

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

}