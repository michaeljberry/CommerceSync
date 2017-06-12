<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'bc/bcvar.php';

$filter = array(
    'min_date_created' => date('r', strtotime("-3 days")),
    'status_id' => 11
);
$bcord->get_bc_orders($BC,$filter, $bc_store_id, $ecommerce, $ibmdata);

$end_time = microtime(true);
$execution_time = ($end_time - $start_time)/60;
echo "Execution time: $execution_time mins";