<?php

use models\channels\Listing;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'ecd/ecdvar.php';

//ob_start();

$start = startClock();
$userID = 838;

$table = 'listing_ecd';
$vaidata = IBM::getBigCommerceInventory();

ecom::dd($vaidata);

$folder = '/var/www/html/portal/';
$log_file_name = date('ymd-H-i') . ' - VAI Inventory.txt';
$inventory_log = $folder . 'log/inventory/' . $log_file_name;
echo "Updated SKU's: Stock_QTY" . PHP_EOL;

$currentSkuInventoryLevels = Listing::getInventory($table);

//ecom::dd($currentSkuInventoryLevels);

foreach ($vaidata as $v) {
    $sku = trim($v['ITITEM']);
    $qty = (int)$v['QTY'];

    if (array_key_exists($sku, $currentSkuInventoryLevels)) {
        if ((int)$currentSkuInventoryLevels[$sku]['inventory_level'] !== $qty) {
            $result = Listing::updateInventory($sku, $qty, $table);
            if ($result) {
                echo $sku . ' is updated.<br />';
            }
            echo "SKU: $sku; Old Level: {$currentSkuInventoryLevels[$sku]['inventory_level']}; New Level: $qty<br>";
        }
    }else{
        $result = Listing::updateInventory($sku, $qty, $table);
        if ($result) {
            echo $sku . ' was added.<br />';
        }
        echo "SKU: $sku; New Level: $qty<br>";
    }
}
endClock($start);
//$content = ob_get_contents();
//ob_end_clean();
//file_put_contents($inventory_log, $content, FILE_APPEND);
