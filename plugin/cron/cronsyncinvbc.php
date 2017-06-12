<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$user_id = 838;
require WEBPLUGIN . 'bc/bcvar.php';

$bigcommerce->get_bc_products($BC, $bc_store_id, $ecommerce);