<?php
require '../../core/init.php';

use models\channels\OrderStatsModel;
use ecommerce\Ecommerce;

$channel = '';
if (isset($_GET['channel']) && !empty($_GET['channel'])) {
    $channel = htmlentities($_GET['channel']);
}
$results = OrderStatsModel::getOrderStats($channel);

$jsonarray2 = Ecommerce::prepareStatJson($results, 'daily');
//\ecommerceclass\ecommerceclass::dd(json_encode($jsonarray2));
echo json_encode($jsonarray2);