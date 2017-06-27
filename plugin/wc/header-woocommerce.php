<?php
require '../../core/init.php';
include_once WEBPLUGIN . 'wc/wcvar.php';
//${channel}_page set so menu shows with default checked
$wc_page = true;
$channel_page = 'woocommerce';
include '../marketplaces/marketplace-menu.php';