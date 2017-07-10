<?php
include_once '../../core/config.php';

//Declare EcomDash Variables
$ecdinfo = $ecd->get_ecd_app_info($user_id);
$ecd_id = $ecdinfo['id'];
$ecd_ocp_key = Crypt::decrypt($ecdinfo['ocp_apim_sub_key']);
$ecd_sub_key = Crypt::decrypt($ecdinfo['ecd_sub_key']);

//echo $ecd_ocp_key . ', ' . $ecd_sub_key;
$ecd_warehouses = $ecd->get_warehouse_ids($ecd_id);
//print_r($ecd_warehouses);

$warehouseId = [];
foreach ($ecd_warehouses as $w) {
    $warehouseId[$w['id']] = [
        'warehouse_id' => $w['warehouse_id'],
        'warehouse_name' => $w['warehouse_name']
    ];
}
echo '<br><br>';
print_r($warehouseId);