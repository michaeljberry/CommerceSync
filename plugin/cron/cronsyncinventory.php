<?php
use ecommerce\Ecommerce;
use models\channels\Category;
use models\channels\Listing;

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
$user_id = 838;

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';

use Ebay\EbayInventory;

$bigcommerce->configure($BC);

$channelToSyncFrom = [
    'table' => 'listing_amazon',
    'category_column' => 'categories_amazon_id'
];
$channelToSyncTo = [
    'table' => 'listing_ebay',
    'category_column' => 'categories_ebay_id'
];

$eligible_products = Listing::syncFromTo($channelToSyncFrom['table'], $channelToSyncTo['table']);
//print_r($eligible_products);
$x = 0;
$page = 34;
$bc_array = [];
foreach ($eligible_products as $p) {
//    if($x > 4){
//        continue;
//    }
    $title = $p['title'];
    $title = html_entity_decode($title);
    $description = $p['description'];
    if (strlen(trim($description)) == 0 || empty($description)) {
        $description = $title;
    }
    $description = htmlentities($description);
    $upc = $p['upc'];
    $sku = $p['sku'];
    $price = $p['price'];
    $photoUrl = "https://4dae45140096fd7fb6d3-7cac89ee19f3b4d177ef11effcca7827.ssl.cf1.rackcdn.com/images/$sku.jpg";
    $quantity = $p['quantity'];
    $categoryID = $p['category_id'];
    $weight = $p['weight'];
    echo "$title $upc $sku $categoryID: ";
    $categoryID = Category::getMappedById($channelToSyncFrom['category_column'], $channelToSyncTo['category_column'], $categoryID);
    echo $categoryID . '<br>';
    //Need to check if item is non-stock in VAI before listing
    $result = IBM::findNonstockItem($sku);
    if (!empty($result)) {
        if (strpos($channelToSyncTo['table'], 'ebay') !== false) {
            $response = EbayInventory::createEbayListing($categoryID, $title, $description, $upc, $sku, $photoUrl,
                $quantity, $price);
            echo '<br><br>';
//            print_r($response);
            $itemID = Ecommerce::substring_between($response, '<itemid>', '</itemid>');
            $errors = Ecommerce::substring_between($response, '<errors>', '</errors>');
            echo "Item ID: $itemID<br><br>";
            if ($errors) {
                print_r($errors);
            } else {
                $listing_id = EbayInventory::saveEbayListing($itemID);
                echo "$sku: UPC - $upc; ListingID - $listing_id<img src='$photoUrl' width='200' height='200'><br>";
//                echo "$sku: UPC - $upc; ListingID - $itemID<img src='$photoUrl' width='200' height='200'><br>";
            }
        } elseif (strpos($channelToSyncTo['table'], 'bigcommerce') !== false) {
            //Add product
            $categoryArray = [$categoryID];
            $photoSku = $sku;
            if (strpos($photoSku, '#') >= 0) {
                $photoSku = str_replace('#', '', $photoSku);
            }
            $photoUrl = "$photoSku.jpg";
            $addResult = [
                $title,
                'P',
                'Y',
                'by product',
                $categoryArray,
                $price,
                $weight,
                $description,
                $sku,
                $upc,
                $photoUrl
            ];
            $bc_array[] = $addResult;
//            $bcinv->add_item($title, $categoryArray, $price, $weight, $description, $sku, $upc, $BC); //$bc_username, $bc_api_key
//            $result = $bcinv->get_product_id_by_sku($sku, $page, $bc_username, $bc_api_key);
//            if($result) {
//                $store_listing_id = $result['p_id'];
//                $page = $result['page'];
//                echo "Page: $page<br><br>";
//                if(!empty($store_listing_id)){
//                    echo "$sku: ID - $store_listing_id Added<br>";
//                    $product_images = $bcinv->get_product_images($store_listing_id, $bc_username, $bc_api_key);
//                    if(empty($product_images)){
//                        print_r($product_images);
//                        //Add product image
////                        $result2 = $bcinv->add_product_image($store_listing_id, $photoUrl, $bc_username, $bc_api_key);
////                        echo "$sku: Image added successfully<br>";
//                    }
//                    //Add brand
//                    //Add publisher
//
//                    //Add inventory
//                    $inventory_filter = [
//                        'inventory_level' => $quantity
//                    ];
//                    $result3 = $bcinv->post_inventory_update($store_listing_id, $inventory_filter, $bc_username, $bc_api_key);
//                    if($result3) {
//                        echo "$sku's inventory has been updated<br>";
//                    }
//                }else{
//                    echo "$sku: Not Added successfully<br>";
//                }
//            }else{
//                echo "No Page: $page<br><br>";
//                break;
//            }
        }
    } else {
        echo "Sku: $sku is an inactive sku";
    }
    echo '<br><br><br><br>';
//    $x++;
}
//print_r($bc_array);
$file = '/home/marketing/BCItems.csv';
//header("Content-Type: text/csv;charset=utf-8");
//header("Content-Disposition: attachment;filename=\"$file\"");
//header("Pragma: no-cache");
//header("Expires: 0");

$fp = fopen($file, 'w');
foreach ($bc_array as $fields) {
    $result = [];
    array_walk_recursive($fields, function ($item) use (&$result) {
        $result[] = $item;
    });
    fputcsv($fp, $result);
}
fclose($fp);