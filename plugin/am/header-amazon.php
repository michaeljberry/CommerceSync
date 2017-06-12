<?php
require '../../core/init.php';
include_once WEBPLUGIN . 'am/amvar.php';
//${channel}_page set so menu shows with default checked
$amazon_page = true;
$channel_page = 'amazon';
include '../marketplaces/marketplace-menu.php';
?>