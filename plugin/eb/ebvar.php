<?php
include_once '../../core/config.php';

//eBay Classes
require WEBCLASSES . 'eb/ebclient.php';
require WEBCLASSES . 'eb/ebclass.php';
require WEBCLASSES . 'eb/ebordclass.php';
require WEBCLASSES . 'eb/ebinvclass.php';

//eBay Class Declarations
$ebclient = new \eb\ebclient($user_id);
$ebay = new \eb\ebayclass($ebclient);
$ebord = new \ebord\ebordclass($ebclient);
$ebinv = new \ebinv\ebinvclass($ebclient);