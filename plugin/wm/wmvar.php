<?php
$WalmartClient = \Walmart\WalmartClient::instance($userID);
$wm = new \Walmart\Walmart($WalmartClient);
$wmord = new \Walmart\WalmartOrder($WalmartClient);
$wminv = new \Walmart\WalmartInventory($WalmartClient);