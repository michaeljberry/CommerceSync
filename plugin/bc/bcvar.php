<?php
$BC = new \Bigcommerce\Api\Client();

$BigCommerceClient = \bc\BigCommerceClient::instance($userID);
$bigcommerce = new \bc\BigCommerce($BC);
$bcord = new \bc\BigCommerceOrder($BC);
$bcinv = new \bc\BigCommerceInventory($BC);