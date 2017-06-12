<?php

use ecommerce\Ecommerce as ecom;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

ob_start();

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'ecd/ecdvar.php';

echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";

$table = 'listing_ecd';
$vaidata = $ibmdata->get_ecom_inven();

//ecom::dd($vaidata);

$folder = '/var/www/html/portal/';
$log_file_name = date('ymd-H-i') . ' - VAI Inventory.txt';
$inventory_log = $folder . 'log/inventory/' . $log_file_name;
echo "Updated SKU's: Stock_QTY" . PHP_EOL;

$current_quantities = $ecommerce->get_current_inventory($table);

//ecom::dd($current_quantities);

foreach($vaidata as $v){
    $sku = trim($v['ITITEM']);
    $qty = (int)$v['QTY'];

    if(array_key_exists($sku, $current_quantities)){
        if((int)$current_quantities[$sku]['inventory_level'] !== $qty){
            $result = $ecommerce->update_inventory($sku, $qty, '', $table);
            if($result){
                echo $sku . ' is updated.<br />';
            }
            echo "SKU: $sku; Old Level: {$current_quantities[$sku]['inventory_level']}; New Level: $qty<br>";
        }
    }
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
$execution = "Execution time: $execution_time mins";
echo $execution;
echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";
$content = ob_get_contents();
ob_end_clean();
file_put_contents($inventory_log, $content, FILE_APPEND);