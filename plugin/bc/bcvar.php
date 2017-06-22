<?php
include_once '../../core/config.php';

$BC = new \Bigcommerce\Api\Client();

//Declare BigCommerce Variables
$BigCommerceClient = new \bc\BigCommerceClient($user_id);
$bigcommerce = new \bc\BigCommerce($BigCommerceClient, $BC);
$bcord = new \bc\BigCommerceOrder($BigCommerceClient, $BC);
$bcinv = new \bc\BigCommerceInventory($BigCommerceClient, $BC);