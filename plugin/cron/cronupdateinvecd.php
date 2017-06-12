<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

ob_start();

$start_time = microtime(true);
echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";

$user_id = 838;

require WEBPLUGIN . 'ecd/ecdvar.php';

$table = 'listing_ecd';

$folder = '/var/www/html/portal/';
$log_file_name = date('ymd-H-i') . ' - ECD Inventory.txt';
$inventory_log = $folder . 'log/inventory/' . $log_file_name;
echo "Updated SKU's: Stock_QTY" . PHP_EOL;

$updated = $ecommerce->get_updated_inventory($table);
$qohArray = [];
for($x = 1; $x <= count($updated); $x++){
    if ($x % 25 == 0 || $x >= count($updated)) {
        $response = $ecdinv->update_ecd_inventory($ecd_ocp_key, $ecd_sub_key, $qohArray, $ecommerce);
        print_r($response);
        echo "Line: $x<br>";
        $qohArray = [];
    }
    $sku = trim($updated[$x]['sku']);
    $qoh1 = $updated[$x]['qty'];
    $whid = $warehouseId[1]['warehouse_id'];
    $qohArray[] = [
        "Sku" => $sku,
        "Quantity" => $qoh1,
        "WarehouseId" => $whid
    ];
    echo "SKU: $sku; QOH:$qoh1<br>" . PHP_EOL;
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
$execution = "Execution time: $execution_time mins";
echo $execution;
echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";
$content = ob_get_contents();
ob_end_clean();
file_put_contents($inventory_log, $content, FILE_APPEND);