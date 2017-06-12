<?php
require '../../core/init.php';

use controllers\channels\OrderStatsController;

$channel = '';
if(isset($_GET['channel']) && !empty($_GET['channel'])){
    $channel = htmlentities($_GET['channel']);
}
$results = OrderStatsController::getOrderStats($channel);

$jsonarray2 = \ecommerce\Ecommerce::prepareStatJson($results, 'daily');
//\ecommerceclass\ecommerceclass::dd(json_encode($jsonarray2));
echo json_encode($jsonarray2);