<?php
use ecommerce\Ecommerce;
use models\channels\Inventory;
use models\channels\Listing;
use models\channels\SKU;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

$table = 'listing_ebay';
$vaidata = IBM::getEbayInventory();

foreach ($vaidata as $v) {
    $sku = $v['ITEM'];
    $qty = $v['QTY'];
    $price = $v['PRICE'];

    $result = Listing::updateInventoryAndPrice($sku, $qty, $price, $table);
    if ($result) echo $sku . ' is updated.<br />';
}
$folder = '/var/www/html/portal/';
$log_file_name = date('ymd') . ' - eBay Inventory.txt';
$inventory_log = $folder . 'log/inventory/' . $log_file_name;
touch($inventory_log);
chown($inventory_log, 'www-data');
chgrp($inventory_log, 'www-data');
chmod($inventory_log, 0775);
$fp = fopen($inventory_log, 'a+');
fwrite($fp, "------------------" . date("Y/m/d H:i:s") . substr((string)$start_time, 1, 6) . "------------------" . PHP_EOL);
fwrite($fp, "Updated SKU's: Stock_QTY" . PHP_EOL);

$updated = Listing::getAll($table);
print_r($updated);
echo '<br><br>';

foreach ($updated as $u) {
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $u['sku'];
    fwrite($fp, $sku . ': ' . $stock_qty . PHP_EOL);
    $price = Listing::getPriceBySKU($sku, $table);
    if (empty($price)) {
        $price = '';
    }

    $response = $ebinv->update_ebay_inventory($stock_id, $stock_qty, $price, $ecommerce);
    print_r($response);

    fwrite($fp, "Inventory Upload Response: " . PHP_EOL . $response . PHP_EOL . PHP_EOL);
    echo '<br>';
}

fclose($fp);

endClock($start);