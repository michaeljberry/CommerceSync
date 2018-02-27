<?php
error_reporting(-1);

use ecommerce\Ecommerce;
use models\channels\Listing;
use Amazon\AmazonInventory;

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';

$start = startClock();

$sku = "121876";
$quantity = 2;

// $table = 'listing_amazon';

// $updated = Listing::getAll($table);

// $x = 1;
// $y = 1;
// $amazon_xml = '';
// $amazon_price_xml = '';
// foreach ($updated as $u) {
//     if ($x > 1) {
//         break;
//     }
//     $stock_id = $u['id'];
//     $sku_id = $u['sku_id'];
//     $stock_qty = $u['stock_qty'];
//     $sku = $u['sku'];
//     $asin = $u['asin1'];
//     $sku = 'WECKL'; //WB, VHD4
//     echo "sku:$sku<br>";
//     $amazon_xml .= AmazonInventory::updateShippingPrice($sku, '3.99', $y);

//     $x++;
//     $y++;
// }

// Ecommerce::dd($amazon_xml);
// $response = AmazonInventory::updateAmazonInventory($amazon_xml);
// Ecommerce::dd($response);

endClock($start);