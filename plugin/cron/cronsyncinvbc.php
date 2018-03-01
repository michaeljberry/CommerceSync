<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'bc/bcvar.php';

$start = startClock();
$user_id = 838;

$bigcommerce->get_bc_products($BC);

endClock($start);