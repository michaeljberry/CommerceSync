<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

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
    $response = $ebinv->update_all_ebay_inventory($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $listing_id); //$price
    print_r($response);
    echo '<br><br>';
//    $x++;
}

endClock($start);