<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

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

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";