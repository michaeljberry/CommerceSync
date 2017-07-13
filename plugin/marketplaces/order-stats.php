<?php
require '../../core/init.php';

use models\channels\order\OrderStats;

$channel = '';
if (isset($_GET['channel']) && !empty($_GET['channel'])) {
    $channel = htmlentities($_GET['channel']);
}
$results = OrderStats::get($channel);

$jsonarray2 = \controllers\channels\order\OrderStatsController::prepareStatJson($results, 'daily');
//\ecommerceclass\ecommerceclass::dd(json_encode($jsonarray2));
echo json_encode($jsonarray2);