<?php
$WalmartClient = \wm\WalmartClient::instance($userID);
$wm = new \wm\Walmart($WalmartClient);
$wmord = new \wm\WalmartOrder($WalmartClient);
$wminv = new \wm\WalmartInventory($WalmartClient);