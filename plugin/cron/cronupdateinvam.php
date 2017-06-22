<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

use ecommerce\Ecommerce;

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';

$table = 'listing_amazon';
//$vaidata = $ibmdata->get_am_inven();
//
//foreach($vaidata as $v){
//    $sku = $v['ITEM'];
//    $qty = $v['QTY'];
//    $price = $v['PRICE'];
//    $result = $ecommerce->update_inventory($sku, $qty, $price, $table);
//    if($result) echo $sku . ' is updated.<br />';
//}
//$folder = '/var/www/html/portal/';
//$log_file_name = date('ymd') . ' - Amazon Inventory.txt';
//$inventory_log = $folder . 'log/inventory/' . $log_file_name;
//touch($inventory_log);
//chown($inventory_log, 'www-data');
//chgrp($inventory_log, 'www-data');
//chmod($inventory_log, 0775);
//$fp = fopen($inventory_log, 'a+');
//fwrite($fp, "------------------" . date("Y/m/d H:i:s").substr((string)$start_time,1,6) . "------------------" . PHP_EOL);
//fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

$updated = $ecommerce->get_inventory_for_update($table);
//print_r($updated);
$x = 1;
$y = 1;
$amazon_xml = '';
$amazon_price_xml = '';
foreach($updated as $u){
    if($x > 1){
        break;
    }
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $u['sku'];
    $asin = $u['asin1'];
    $sku = 'WECKL'; //WB, VHD4
//    fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
//    $price = $ecommerce->get_inventory_price($sku, $table);
//    if(!empty($price)){
//        $amazon_price_xml .= $aminv->create_inventory_price_update_item_xml($sku, $price, $y);
//    }
    //Create XML for Amazon
//    $amazon_xml .= $aminv->create_inventory_update_item_xml($sku, $stock_qty, $x);
//    $amazon_xml .= $aminv->updateTaxCode($sku, $asin, $y);
    echo "sku:$sku<br>";
    $amazon_xml .= $aminv->updateShippingPrice($sku, '3.99', $y);

    $x++;
    $y++;
}

//fwrite($fp, 'Inventory Upload File: ' . PHP_EOL . $amazon_xml. PHP_EOL . PHP_EOL);
//fwrite($fp, 'Price Upload File: ' . PHP_EOL . $amazon_price_xml. PHP_EOL . PHP_EOL);
//\ecommerceclass\ecommerceclass::dd($amazon_xml);
Ecommerce::dd($amazon_xml);
////Push to Amazon
$response = $aminv->updateAmazonInventory($amazon_xml);
Ecommerce::dd($response);
//fwrite($fp, "Inventory Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
//$response = $aminv->update_amazon_inventory_price($amazon_price_xml);
//print_r($response);
//fwrite($fp, "Price Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
//echo '<br>';

//fclose($fp);
endClock($start);