<?php
$BC = new \Bigcommerce\Api\Client();

$BigCommerceClient = \bc\BigCommerceClient::instance($user_id);
$bigcommerce = new \bc\BigCommerce($BC);
$bcord = new \bc\BigCommerceOrder($BC);
$bcinv = new \bc\BigCommerceInventory($BC);