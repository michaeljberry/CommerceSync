<?php
//require PORTALFOLDER . 'plugin/eb/ebvar-sand.php';

//Declare eBay Sand-box Variables
$ebayappid = $ebay->get_ebay_app_id($user_id, true);
$eb_dev_id = $crypt->decrypt($ebayappid['devid']);
$eb_app_id = $crypt->decrypt($ebayappid['appid']);
$eb_cert_id = $crypt->decrypt($ebayappid['certid']);
$eb_token = $crypt->decrypt($ebayappid['token']);
$eb_store_id = $ebayappid['store_id'];
//        echo $eb_dev_id . "<br />";
//        echo $eb_app_id . "<br />";
//        echo $eb_cert_id . "<br />";
//        echo $eb_token . "<br />";