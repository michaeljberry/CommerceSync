<?php
use ecommerce\Ecommerce;
use models\channels\Inventory;
use models\channels\Listing;
use models\channels\SKU;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'am/amvar.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';

$table = 'listing_amazon';

$updated = Listing::getAll($table);
print_r($updated);
echo '<br><br>';
$x = 1;
$amazon_xml = [];
$amazon_price_xml = [];
foreach ($updated as $u) {
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = SKU::getById($sku_id);
    $price = Listing::getPriceBySku($sku, $table);
    if (!empty($price)) {
        $amazon_price_xml = array_merge($amazon_price_xml, $aminv->create_inventory_price_update_item_xml($sku, $price, $x));
    }
    //Create XML for Amazon
    $amazon_xml = array_merge($amazon_xml, $aminv->create_inventory_update_item_xml($sku, $stock_qty, $x));
    $x++;
}
//Push to Amazon
$response = $aminv->updateAmazonInventory($amazon_xml);
print_r($response);
$response = $aminv->updateAmazonInventoryPrice($amazon_price_xml);
print_r($response);
echo '<br>';

endClock($start);