<?php

namespace wm;

use \Walmart\Item as WalmartItem;

class WalmartInventory extends Walmart
{
    public function constructAuthorizationToken()
    {
        $wmitem = new WalmartItem([
            'consumerId' => $this->WalmartClient->getConsumerKey(),
            'privateKey' => $this->WalmartClient->getSecretKey()
        ]);
        return $wmitem;
    }

    public function getItem($sku)
    {
        $wmitem = $this->constructAuthorizationToken();
        $item = $wmitem->get([
            'sku' => $sku
        ]);
        return $item;
    }

}