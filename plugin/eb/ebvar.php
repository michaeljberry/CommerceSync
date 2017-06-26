<?php
include_once '../../core/config.php';

//eBay Class Declarations
$EbayClient = \eb\EbayClient::instance($user_id);
$ebay = new \eb\Ebay($EbayClient);
$ebord = new \eb\EbayOrder($EbayClient);
$ebinv = new \eb\EbayInventory($EbayClient);