<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

use ecommerce\Ecommerce;
use models\channels\Listing;
use models\channels\product\ProductPrice;
use Ebay\EbayInventory;

$start = startClock();

$count = IBM::getCount();
echo "SKU count: $count<br>";

$updatedPrices = ProductPrice::getUpdated();

$reverbListings = Listing::getByChannel('listing_reverb');
$ebayListings = Listing::getByChannel('listing_ebay');
$amazonListings = Listing::getByChannel('listing_amazon');
$bigcommerceListings = Listing::getByChannel('listing_bigcommerce');

$x = 1;
$y = 0;
$amazonXML = [];
$arrayKeys = array_keys($updatedPrices);
$lastArrayKey = array_pop($arrayKeys);
foreach ($updatedPrices as $sku => $prices) {
    // if($sku !== 'CMP153'){
    //     continue;
    // }

    // print_r($prices);

    extract($prices);

    if (array_key_exists($sku, $reverbListings)) {
        echo "Reverb: $sku -> $pl10 -> {$reverbListings[$sku]['id']}<br>";
        $response = $revinv->updateListing($reverbListings[$sku]['id'], $pl10);
        Ecommerce::dd($response);
    }
    if (array_key_exists($sku, $ebayListings)) {
        echo "Ebay: $sku -> $pl10 -> {$ebayListings[$sku]['id']}<br>";
        $response = EbayInventory::updateEbayListingPrice($ebayListings[$sku]['id'], $pl10);
        Ecommerce::dd($response);
    }
    if (array_key_exists($sku, $amazonListings)) {
        echo "Amazon: $sku -> $pl10 -> {$amazonListings[$sku]['id']}<br>";
        $y++;
        $amazonXML = array_merge($amazonXML, $aminv->create_inventory_price_update_item_xml(
            $sku,
            $pl10,
            $y
        ));
        if ($x % 30000 == 0) {
            $response = $aminv->updateAmazonInventoryPrice($amazonXML);
            Ecommerce::dd($response);
            $y = 0;
        }
        $x++;
    }
    if(array_key_exists($sku, $walmartListings)){
        echo "Walmart: $sku -> $pl10 -> {$walmartListings[$sku]['sku']}<br>";

    }
    if (array_key_exists($sku, $bigcommerceListings)) {
        echo "BigCommerce: $sku -> $pl10 -> {$bigcommerceListings[$sku]['id']}<br>";
        $response = $bcinv->updateInventory($bigcommerceListings[$sku]['id'], $pl10);
        Ecommerce::dd($response);
    }
    echo "<br><br>";
}
$response = $aminv->updateAmazonInventoryPrice($amazonXML);
Ecommerce::dd($response);

endClock($start);
