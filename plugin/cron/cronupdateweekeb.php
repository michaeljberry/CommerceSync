<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

$table = 'listing_ebay';

$updated = $ecommerce->get_inventory_weekly($table);
print_r($updated);
echo '<br><br>';

foreach ($updated as $u) {
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $ecommerce->get_sku($sku_id);
    $price = $ecommerce->get_inventory_price($sku, 'listing_ebay');
    if (empty($price)) {
        $price = '';
    }

    $response = $ebinv->update_ebay_inventory($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $stock_id, $stock_qty, $price, $ecommerce);
    print_r($response);
    echo '<br>';
}

endClock($start);