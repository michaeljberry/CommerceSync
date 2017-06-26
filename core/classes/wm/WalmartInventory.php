<?php

namespace wm;

use \Walmart\Item as WalmartItem;

class WalmartInventory extends Walmart
{
    public function construct_auth(){
        $wmitem = new WalmartItem([
            'consumerId' => $this->WalmartClient->getConsumerKey(),
            'privateKey' => $this->WalmartClient->getSecretKey()
        ]);
        return $wmitem;
    }
    public function get_item($sku){
        $wmitem = $this->construct_auth();
        $item = $wmitem->get([
            'sku' => $sku
        ]);
        return $item;
    }

}