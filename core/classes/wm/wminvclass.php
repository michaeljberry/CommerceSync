<?php

namespace wminv;

use \Walmart\Item as WalmartItem;
use wm\walmartclass;

class wminvclass extends walmartclass
{
    public function construct_auth($wm_consumer_key, $wm_secret_key){
        $wmitem = new WalmartItem([
            'consumerId' => $wm_consumer_key,
            'privateKey' => $wm_secret_key
        ]);
        return $wmitem;
    }
    public function get_item($wm_consumer_key, $wm_secret_key, $sku){
        $wmitem = $this->construct_auth($wm_consumer_key, $wm_secret_key);
        $item = $wmitem->get([
            'sku' => $sku
        ]);
        return $item;
    }

}