<?php
\ecommerce\Ecommerce::dd('Walmart instance:');
$WalmartClient = \Walmart\WalmartClient::instance($userID);
\ecommerce\Ecommerce::dd($WalmartClient);
$wm = new \Walmart\Walmart($WalmartClient);
$wmord = new \Walmart\WalmartOrder($WalmartClient);
$wminv = new \Walmart\WalmartInventory($WalmartClient);
