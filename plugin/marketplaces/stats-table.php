<?php
require '../../core/init.php';
$channel = '';
if (isset($_GET['channel']) && !empty($_GET['channel'])) {
    $channel = htmlentities($_GET['channel']);
}
$results = \controllers\channels\order\OrderStatsController::stats_table($channel);