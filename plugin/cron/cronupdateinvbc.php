<?php

use Ecommerce\Ecommerce;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'bc/bcvar.php';

$table = 'listing_bigcommerce';
$vaidata = IBM::getBigCommerceInventory();

foreach ($vaidata as $v) {
    $sku = $v['ITEM'];
    $qty = $v['QTY'];
    $price = $v['PRICE'];
//    $result = Ecommerce::update_inventory($sku, $qty, $price, $table);
//    if($result) echo $sku . ' is updated.<br />';
    echo "$sku: $qty -> $price<br>";
}
//$folder = '/var/www/html/portal/';
//$log_file_name = date('ymd') . ' - MML Inventory.txt';
//$inventory_log = $folder . 'log/inventory/' . $log_file_name;
//touch($inventory_log);
//chown($inventory_log, 'www-data');
//chgrp($inventory_log, 'www-data');
//chmod($inventory_log, 0775);
//$fp = fopen($inventory_log, 'a+');
//fwrite($fp, "------------------" . date("Y/m/d H:i:s").substr((string)$start_time,1,6) . "------------------" . PHP_EOL);
//fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);
//
//$updated = Ecommerce::get_inventory_for_update($table);
//print_r($updated);
//echo '<br><br>';
//
//foreach($updated as $u){
//    $stock_id = $u['id'];
//    $sku_id = $u['sku_id'];
//    $stock_qty = $u['stock_qty'];
//    $sku = Ecommerce::get_sku($sku_id);
//    fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
//    $price = Ecommerce::get_inventory_price($sku, $table);
//    if(empty($price)){
//        $price = '';
//    }
//
//    $response = $bcinv->update_bc_inventory($stock_id, $stock_qty, $price);
//    print_r($response);
//
//    fwrite($fp, 'Inventory Upload Response: ' . PHP_EOL . $response. PHP_EOL . PHP_EOL);
//    echo '<br>';
//}
//
//fclose($fp);
endClock($start);