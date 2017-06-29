<?php
use ecommerce\Ecommerce;
use models\channels\OrderStats;

require '../../core/init.php';

if (isset($_GET['sku_id']) && !empty($_GET['sku_id'])) {
    $sku_id = htmlentities($_GET['sku_id']);

    $ourSalesHistory = OrderStats::getSalesHistory($sku_id);

    $jsonarray2 = \controllers\channels\OrderStatsController::prepareStatJson($ourSalesHistory, 'monthly');
    echo json_encode($jsonarray2);
}