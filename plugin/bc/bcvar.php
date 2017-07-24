<?php
$BC = new \Bigcommerce\Api\Client();

$BigCommerceClient = \BigCommerce\BigCommerceClient::instance($userID);
$bigcommerce = new \BigCommerce\BigCommerce($BC);
$bcord = new \BigCommerce\BigCommerceOrder($BC);
$bcinv = new \BigCommerce\BigCommerceInventory($BC);