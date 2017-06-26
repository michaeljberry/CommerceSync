<?php
include_once '../../core/config.php';

//eBay Class Declarations
$EbayClient = \eb\EbayClient::instance($user_id);
$ebay = new \eb\Ebay();
$ebord = new \eb\EbayOrder();
$ebinv = new \eb\EbayInventory();