<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

use Ebay\EbayInventory;

$results = $ebay->get_listings();
//print_r($results);
//$x = 1;
foreach ($results as $r) {
//    if($x > 2){
//        continue;
//    }
    $listing_id = $r['store_listing_id'];
    $price = $r['price'];
    echo $listing_id . ': ' . $price . '<br>';
    $response = EbayInventory::updateEbayListingPrice($listing_id, $price); //$price
    print_r($response);
    echo '<br><br>';
//    $x++;
}

endClock($start);
