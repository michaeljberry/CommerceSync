<?php
use models\channels\Inventory;
use models\channels\Listing;
use models\channels\SKU;

error_reporting(-1);
require __DIR__ . '/../../core/init.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'bc/bcvar.php';

$table = 'listing_bigcommerce';

$updated = Listing::getAll($table);
print_r($updated);
echo '<br><br>';

foreach ($updated as $u) {
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = SKU::getById($sku_id);
    $price = Listing::getPriceBySku($sku, $table);
    if (empty($price)) {
        $price = '';
    }

    $response = $bcinv->update_bc_inventory($stock_id, $stock_qty, $price);
    print_r($response);
    echo '<br>';
}

endClock($start);