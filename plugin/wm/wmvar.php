<?php

use Walmart\Order as WalmartOrder;
use Walmart\Item as WalmartItem;
use Walmart\Feed as WalmartFeed;
//Declare Walmart Variables
$walmartinfo = $wm->get_wm_app_info($user_id);
$wm_store_id = $walmartinfo['store_id'];
$wm_consumer_key = $crypt->decrypt($walmartinfo['consumer_id']);
$wm_secret_key = $crypt->decrypt($walmartinfo['secret_key']);
$wm_api_header = $walmartinfo['api_header'];

//echo "Consumer ID: $wm_consumer_key<br>Secret Key: $wm_secret_key<br>Header: $wm_api_header<br><br>";
$wmorder = new WalmartOrder([
    'consumerId' => $wm_consumer_key,
    'privateKey' => $wm_secret_key,
    'wmConsumerChannelType' => $wm_api_header
]);

//$wmitems = new WalmartItem([
//    'consumerId' => $wm_consumer_key,
//    'privateKey' => $wm_secret_key,
//]);