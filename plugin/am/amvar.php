<?php
include_once '../../core/config.php';

//Amazon Class Declarations
$amclient = new \am\AmazonClient($user_id);
$amazon = new \am\Amazon($amclient);
$amord = new \am\AmazonOrder($amclient);
$aminv = new \am\AmazonInventory($amclient);