<?php
use ecommerce\Ecommerce;
use models\channels\Listing;

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

$results = IBM::getSKUToDelete();
//$x = 0;
foreach ($results as $r) {
//    if($x > 5){
//        continue;
//    }
    $sku = trim($r['IFITEM']);
    $listing_id = Listing::getIdBySKU($sku, 'listing_ebay');
    $result = $ebinv->deleteItem($listing_id);
    echo "$sku: <br>";
    print_r($result);
    echo '<br><br>';
//    $x++;
}