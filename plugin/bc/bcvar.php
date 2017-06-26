<?php
include_once '../../core/config.php';

$BC = new \Bigcommerce\Api\Client();

//Declare BigCommerce Variables
$BigCommerceClient = \bc\BigCommerceClient::instance($user_id);
$bigcommerce = new \bc\BigCommerce($BC);
$bcord = new \bc\BigCommerceOrder($BC);
$bcinv = new \bc\BigCommerceInventory($BC);