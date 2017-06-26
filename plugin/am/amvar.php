<?php
include_once '../../core/config.php';

//Amazon Class Declarations
$AmazonClient = \am\AmazonClient::instance($user_id);
$amazon = new \am\Amazon();
$amord = new \am\AmazonOrder();
$aminv = new \am\AmazonInventory();