<?php
$WalmartClient = \wm\WalmartClient::instance($user_id);
$wm = new \wm\Walmart($WalmartClient);
$wmord = new \wm\WalmartOrder($WalmartClient);
$wminv = new \wm\WalmartInventory($WalmartClient);