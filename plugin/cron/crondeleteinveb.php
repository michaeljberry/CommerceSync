<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

$results = IBM::getSKUToDelete();
//$x = 0;
foreach($results as $r){
//    if($x > 5){
//        continue;
//    }
    $sku = trim($r['IFITEM']);
    $listing_id = $ecommerce->get_listing_id_by_sku($sku, 'listing_ebay');
    $result = $ebinv->deleteItem($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $listing_id);
    echo "$sku: <br>";
    print_r($result);
    echo '<br><br>';
//    $x++;
}