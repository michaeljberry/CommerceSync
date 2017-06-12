<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
include_once WEBPLUGIN . 'am/amvar.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';

$table = 'listing_amazon';

$updated = $ecommerce->get_inventory_weekly($table);
print_r($updated);
echo '<br><br>';
$x = 1;
$amazon_xml = '';
$amazon_price_xml = '';
foreach($updated as $u){
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $ecommerce->get_sku($sku_id);
    $price = $ecommerce->get_inventory_price($sku, $table);
    if(!empty($price)){
        $amazon_price_xml .= $aminv->create_inventory_price_update_item_xml($sku, $price, $x);
    }
    //Create XML for Amazon
    $amazon_xml .= $aminv->create_inventory_update_item_xml($sku, $stock_qty, $x);
    $x++;
}
//Push to Amazon
$response = $aminv->updateAmazonInventory($amazon_xml);
print_r($response);
$response = $aminv->updateAmazonInventoryPrice($amazon_price_xml);
print_r($response);
echo '<br>';

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";