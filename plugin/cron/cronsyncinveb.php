<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$user_id = 838;
require WEBPLUGIN . 'eb/ebvar.php';

$ebinv->sync_ebay_products($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $eb_store_id, $ecommerce);