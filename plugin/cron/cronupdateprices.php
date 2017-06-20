<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

use ecommerce\Ecommerce as ecom;

$start = startClock();

$count = $ibmdata->get_count();
echo $count . '<br>';

$updatedPrices = $ecommerce->get_inventory_prices(15);

$reverbListings = ecom::getChannelListingsFromDB('reverb');
$ebayListings = ecom::getChannelListingsFromDB('ebay');
$amazonListings = ecom::getChannelListingsFromDB('amazon');
$bigcommerceListings = ecom::getChannelListingsFromDB('bigcommerce');

$x = 1;
$y = 0;
$amazonXML = '';
$arrayKeys = array_keys($updatedPrices);
$lastArrayKey = array_pop($arrayKeys);
foreach($updatedPrices as $key => $prices){
//    if($x > 6){
//        break;
//    }
    extract($prices);

    if(array_key_exists($key, $reverbListings)){
        echo "Reverb: $key -> $pl10 -> {$reverbListings[$key]['id']}<br>";
        $response = $revinv->updateListing($reverbListings[$key]['id'], $pl10);
        ecom::dd($response);
    }
    if(array_key_exists($key, $ebayListings)){
        echo "Ebay: $key -> $pl10 -> {$ebayListings[$key]['id']}<br>";
        $response = $ebinv->update_all_ebay_inventory($ebayListings[$key]['id'], $pl10);
        ecom::dd($response);
    }
    if(array_key_exists($key, $amazonListings)){
        echo "Amazon: $key -> $pl10 -> {$amazonListings[$key]['id']}<br>";
        $y++;
        $amazonXML .= $aminv->create_inventory_price_update_item_xml(
            $key,
            $pl10,
            $y
        );
        if($x % 30000 == 0){
            $response = $aminv->updateAmazonInventoryPrice($amazonXML);
            ecom::dd($response);
            $y = 0;
        }
        $x++;
    }
    if(array_key_exists($key, $bigcommerceListings)){
        echo "BigCommerce: $key -> $pl10 -> {$bigcommerceListings[$key]['id']}<br>";
        $response = $bcinv->updateInventory($bigcommerceListings[$key]['id'], $pl10);
        ecom::dd($response);
    }
    echo "<br><br>";
}
$response = $aminv->updateAmazonInventoryPrice($amazonXML);
ecom::dd($response);

endClock($start);