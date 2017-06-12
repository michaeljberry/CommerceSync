<?php
include_once '../../core/config.php';

//BigCommerce Classes
include_once WEBCLASSES . 'bc/bcclient.php';
include_once WEBCLASSES . 'bc/bcclass.php';
include_once WEBCLASSES . 'bc/bcordclass.php';
include_once WEBCLASSES . 'bc/bcinvclass.php';

$BC = new \Bigcommerce\Api\Client();

//Declare BigCommerce Variables
$bcclient = new \bc\bcclient($user_id);
$bigcommerce = new \bc\bigcommerceclass($bcclient);
$bcord = new \bcord\bcordclass($bcclient);
$bcinv = new \bcinv\bcinvclass($bcclient);

$bigcommerceinfo = $bigcommerce->get_bc_app_info($user_id);
$bc_store_id = $bigcommerceinfo['store_id'];

$bigcommerce->configure($BC);