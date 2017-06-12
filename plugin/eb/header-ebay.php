<?php
require '../../core/init.php';
include_once WEBPLUGIN . 'eb/ebvar.php';
//${channel}_page set so menu shows with default checked
$ebay_page = true;
$channel_page = 'ebay';
include '../marketplaces/marketplace-menu.php';
?>