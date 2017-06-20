<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'eb/ebvar.php';

$start = startClock();
$user_id = 838;

$results = $ebay->get_listing_upc();
//print_r($results);
//$x = 1;
foreach($results as $r){
//    if($x > 2){
//        continue;
//    }
    $listing_id = $r['store_listing_id'];
    $sku = $r['sku'];
    $upc = $r['upc'];
    $response = $ebinv->edit_gtin($listing_id, $upc, $sku);
    print_r($response);
    echo '<br><br>';
//    $x++;
}
endClock($start);