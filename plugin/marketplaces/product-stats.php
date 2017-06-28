<?php
require '../../core/init.php';

$channel = '';
if (isset($_GET['sku_id']) && !empty($_GET['sku_id'])) {
    $sku_id = htmlentities($_GET['sku_id']);
}

$ourSalesHistory = $ecommerce->getSalesHistory($sku_id);

$jsonarray2 = \controllers\channels\OrderStatsController::prepareStatJson($ourSalesHistory, 'monthly');
//\ecommerceclass\ecommerceclass::dd(json_encode($jsonarray2));
echo json_encode($jsonarray2);