<?php
include_once '../../core/config.php';

//eBay Classes
//require WEBCLASSES . 'eb/ebclient.php';
//require WEBCLASSES . 'eb/Ebay.php';
//require WEBCLASSES . 'eb/ebordclass.php';
//require WEBCLASSES . 'eb/ebinvclass.php';

//eBay Class Declarations
$ebclient = new \eb\EbayClient($user_id);
$ebay = new \eb\Ebay($ebclient);
$ebord = new \eb\EbayOrder($ebclient);
$ebinv = new \eb\EbayInventory($ebclient);