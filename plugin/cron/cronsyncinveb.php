<?php
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'eb/ebvar.php';

use Ebay\EbayInventory;

EbayInventory::downloadEbayListings();