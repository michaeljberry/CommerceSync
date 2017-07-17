<?php
error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'bc/bcvar.php';

$start = startClock();
$user_id = 838;

$filter = array(
    'min_date_created' => date('r', strtotime("-3 days")),
    'status_id' => 11
);
$bcord->get_bc_orders($BC, $filter, $ecommerce);

endClock($start);