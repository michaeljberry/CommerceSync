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

$updated = Listing::getAll($table);
print_r($updated);
echo '<br><br>';

foreach ($updated as $u) {
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = SKU::getById($sku_id);
    $price = Listing::getPriceBySKU($sku, 'listing_ebay');
    if (empty($price)) {
        $price = '';
    }

    $response = $ebinv->update_ebay_inventory($stock_id, $stock_qty, $price, $ecommerce);
    print_r($response);
    echo '<br>';
}

endClock($start);