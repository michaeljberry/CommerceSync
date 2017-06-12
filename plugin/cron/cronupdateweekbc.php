<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'bc/bcvar.php';

$table = 'listing_bigcommerce';

$updated = $ecommerce->get_inventory_weekly($table);
print_r($updated);
echo '<br><br>';

foreach($updated as $u){
    $stock_id = $u['id'];
    $sku_id = $u['sku_id'];
    $stock_qty = $u['stock_qty'];
    $sku = $ecommerce->get_sku($sku_id);
    $price = $ecommerce->get_inventory_price($sku, $table);
    if(empty($price)){
        $price = '';
    }

    $response = $bcinv->update_bc_inventory($stock_id, $stock_qty, $price, $ecommerce);
    print_r($response);
    echo '<br>';
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";