<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'wc/wcvar.php';

//$listing = $wcinv->get_listings($woocommerce, 25837);
//print_r($listing);
//
//$qty = 30;
//$price = 14.95;
//
//$filter = [
////    'managing_stock' => 1,
//    'stock_quantity' => $qty,
////    'in_stock' => 1,
//    'regular_price' => $price,
////    'price' => '14.95'
//];
//$update = $wcinv->update_listing($woocommerce, 25837, $listing, $filter);
//print_r($update);
//
////$listings = $wcinv->get_listings($woocommerce, 25837);
////print_r($listings);

$table = 'listing_wc';
$vaidata = $ibmdata->get_wc_inven();

foreach($vaidata as $v){
    $sku = $v['ITEM'];
    $qty = $v['QTY'];
    $price = $v['PRICE'];
    $result = $ecommerce->update_inventory($sku, $qty, $price, $table);
    if($result) echo $sku . ' is updated.<br />';
}
$folder = '/var/www/html/portal/';
$log_file_name = date('ymd') . ' - WC Inventory.txt';
$inventory_log = $folder . 'log/inventory/' . $log_file_name;
touch($inventory_log);
chown($inventory_log, 'www-data');
chgrp($inventory_log, 'www-data');
chmod($inventory_log, 0775);
$fp = fopen($inventory_log, 'a+');
fwrite($fp, "------------------" . date("Y/m/d H:i:s").substr((string)$start_time,1,6) . "------------------" . PHP_EOL);
fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

$updated = $ecommerce->get_inventory_for_update($table);
print_r($updated);
echo '<br><br>';

foreach($updated as $u){
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $ecommerce->get_sku($sku_id);
    fwrite($fp, $sku . ': ' . $stock_qty . ', ID: ' . $stock_id . PHP_EOL);
    $price = $ecommerce->get_inventory_price($sku, $table);
    if(empty($price)){
        $price = '';
    }

    $response = $wcinv->updateInventory($stock_id, $stock_qty, $price, $ecommerce, $woocommerce, $sku);
    print_r($response);
    extract($response);
//    $id = $response['product']['id'];
//    $title = $response['product']['title'];
//    $sku = $response['product']['sku'];
//    $price = $response['product']['price'];
//    $stock_quantity = $response['product']['stock_quantity'];
//    $managing_stock = $response['product']['managing_stock'];
//    $in_stock = $response['product']['in_stock'];
    $fileResponse = 'ID: ' . $id . ', Name: ' . $title . ', SKU: ' . $sku . ', Price: ' . $price  . ', Stock Qty: ' . $stock_quantity . ', Manage Stock: ' .$managing_stock . ', In Stock: ' . $in_stock;
    echo $fileResponse;

    fwrite($fp, 'Inventory Upload Response: ' . PHP_EOL . $fileResponse. PHP_EOL . PHP_EOL);
    echo '<br>';
}

fclose($fp);
endClock($start);