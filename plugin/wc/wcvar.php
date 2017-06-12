<?php
error_reporting(-1);

//WooCommerce Classes
include_once WEBCLASSES . 'wc/wcclass.php';
include_once WEBCLASSES . 'wc/wcordclass.php';
include_once WEBCLASSES . 'wc/wcinvclass.php';

//WooCommerce Class Declarations
$wc = new \wc\woocommerceclass($user_id);
$wcord = new \wcord\wcordclass($user_id);
$wcinv = new \wcinv\wcinvclass($user_id);

$woocommerce = $wc->configure();

//Declare WC Variables
//$wcinfo = $wc->get_wc_app_id($user_id);
//$wc_consumer_key = $crypt->decrypt($wcinfo['consumer_key']);
//$wc_secret_key = $crypt->decrypt($wcinfo['consumer_secret']);
//$wc_site = $wcinfo['site'];
//$wc_store_id = $wcinfo['store_id'];
//    echo $wc_consumer_key . "<br />";
//    echo $wc_secret_key . "<br />";
//    echo $wc_site . "<br />";
//    echo $wc_store_id . "<br />";
//    $wc_array[] = 'WC' . $store_id;
//$response = $wcinv->create_listing($wc_consumer_key, $wc_secret_key);
//print_r($response);




//try {
//    print_r($woocommerce->get('products'));
//}catch (HttpClientException $e){
//    \print_r($e);
//    \print_r($e->getMessage() . PHP_EOL);
//    \print_r('Code: ' . $e->getResponse()->getCode() . PHP_EOL);
//    \print_r('Body: ' . $e->getResponse()->getBody() . PHP_EOL);
//}
