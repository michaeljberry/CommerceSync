<?php
include_once '../../core/config.php';

//Amazon Class Declarations
$AmazonClient = new \am\AmazonClient($user_id);
$amazon = new \am\Amazon($AmazonClient);
$amord = new \am\AmazonOrder($AmazonClient);
$aminv = new \am\AmazonInventory($AmazonClient);