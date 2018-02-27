<?php
include 'header-marketing.php';

use Ebay\Ebay;

//ini_set('max_execution_time', 3600);
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';
//require WEBCLASSES . 'query/selecttests.php';
//print_r($ebayappid);
if ($user_id == 838) {

    //    $postString = [
    //        'price' => [
    //            'amount' => $price
    //        ]
    //    ];
    //    echo json_encode($postString);

    //    $sku = 'H211';
    //    $asin = 'B000V9PKZA';
    //    $title = 'Hearos Earplugs High Fidelity Series with Free Case, 1 Pair';
    //    $itemID = '222003021254';
    //    $upc = '756063002115';
    //    $sku_id = '8075';
    //
    //
    //    $ourAmazonPrice = simplexml_load_string($aminv->GetMyPriceForSKU($sku));
    //    \ecommerceclass\ecommerceclass::dd($ourAmazonPrice);
    //    $amazonPrice = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->ListingPrice->Amount;
    //    $amazonShipping = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->Shipping->Amount;
    //    $amazonTotal = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->LandedPrice->Amount;
    //
    //    \ecommerceclass\ecommerceclass::dd($amazonPrice);
    //
    //    echo "$amazonPrice + $amazonShipping = $amazonTotal";
    //    $ebayRecentSales = simplexml_load_string($ebinv->findCompletedItems($upc));
    //    Ecommerce::formatChannelRecentSales($ebayRecentSales);

    //    $currentAmazonProducts = simplexml_load_string($aminv->getLowestOfferListingsForSKU($sku));
    //    \ecommerceclass\ecommerceclass::dd($currentAmazonProducts);
    //    $amazonListings = $aminv->sortAmazonSearchResults($currentAmazonProducts);
    //    $arrayToInclude = ['numOfListingsAtThisPrice', 'sellerRating', 'shippingTime', 'price', 'shipping', 'total'];
    //    $label = 'Current Listings on Amazon for same SKU';
    //    echo \ecommerceclass\ecommerceclass::arrayToTable($amazonListings, $arrayToInclude, $label);
    //    \ecommerceclass\ecommerceclass::dd($amazonListings);

    //    $response = simplexml_load_string($ebinv->findCompletedItems($upc));
    //    \ecommerceclass\ecommerceclass::dd($response);

    //    $ourSalesHistory = $ecommerce->getSalesHistory($sku_id);
    //    \ecommerceclass\ecommerceclass::dd($ourSalesHistory);
    //    $jsonarray2 = \ecommerceclass\ecommerceclass::prepareStatJson($ourSalesHistory, 'monthly');
    //    \ecommerceclass\ecommerceclass::dd($jsonarray2);

    //
    //    $sellers = [];
    //    $sellers = $ebinv->sorteBaySearchResults($response);
    ////    \ecommerceclass\ecommerceclass::dd($sellers);
    //    $table = $ebinv->searchResultsTable($sellers);
    //    echo $table;

    //    \ecommerceclass\ecommerceclass::dd($sql);
    //    \ecommerceclass\ecommerceclass::dd(\QB\querybuilder::arrayToQuery($sql));

    //    $xml = [
    //        'Item' =>
    //            [
    //                'Title' => 'The Whiz Bang Awesome Product',
    //                'SKU' => '123456',
    //                'NameValueList' => [
    //                    [
    //                        'Name' => 'Brand',
    //                        'Value' => 'Unbranded'
    //                    ],
    //                    [
    //                        'Name' => 'MPN',
    //                        'Value' => '123456'
    //                    ]
    //                ],
    //                'ShippingDetails' => [
    //                    'ShippingServiceOptions' => [
    //                        [
    //                            'FreeShipping' => 'true',
    //                            'ShippingService' => 'ShippingMethodStandard',
    //                            'ShippingServiceCost~currency=USD' => '0.00',
    //                            'ShippingServiceAdditionalCost' => '0.00',
    //                            'ShippingServicePriority' => '1'
    //                        ],
    //                        [
    //                            'ShippingService' => 'UPSGround',
    //                            'ShippingServiceCost' => '9.99',
    //                            'ShippingServiceAdditionalCost' => '9.99',
    //                            'ShippingServicePriority' => '2'
    //                        ]
    //                    ],
    //                ]
    //            ]
    //    ];
    //    \ecommerce\Ecommerce::dd(\models\channels\XML::makeXML($xml));
    //    print_r($xml);
    //    echo '<br><br>';
    //    $generatedXML = $ebay->xmlOpenTag();
    //    $stock_id = '10';
    //    $quantity = 5;
    //    $price = '3.99';
    //    $listings = $ebay->get_recently_updated_listings();
    //    $x = 0;
    //    foreach($listings as $l){
    //        $item_id = $l['store_listing_id'];
    //        $description = $l['description'];
    //        echo "$item_id -> Description: $description<br><br>";
    //        if($x > 10) break;
    //        $generatedXML = $ebinv->update_all_ebay_inventory($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $item_id, $description);
    //        echo '<br><br><pre>';
    //        print_r($generatedXML);
    //        echo '</pre><br><br>';
    //        $x++;
    //    }


    //
    //    $bigcommerce->configure($BC, $bc_store_url, $bc_username, $bc_api_key);
    //
    //    $filter = array(
    //        'min_date_created' => date('r', strtotime("-3 days")),
    //        'status_id' => 11
    //    );
    //    $bcord->test_get_bc_orders($BC,$filter,$bc_username, $bc_api_key, $bc_store_id, $ecommerce);
    //    $result = $aminv->get_flat_file();
    //    print_r($result);

    //    $bigcommerce->configure($BC, $bc_store_url, $bc_username, $bc_api_key);

    //    $category_count = $bigcommerce->get_category_count($bc_username, $bc_api_key);
    //    print_r($category_count);
    //    $categories = $bigcommerce->get_categories($bc_username, $bc_api_key);
    //    print_r($categories);
    //    foreach($categories as $c){
    //        $cat_id = $c->id;
    //        $p_cat_id = $c->parent_id;
    //        $cat_name = $c->name;
    //        $result = $ecommerce->save_category($cat_id, $cat_name, $p_cat_id, 'categories_bigcommerce');
    //        echo ($result) ? "$cat_id: $cat_name added<br>" : "";
    //    }

    //    $all_bc_products = $bigcommerce->get_product_with_upc();
    //    foreach($all_bc_products as $u){
    //        $sku = $u['sku'];
    //        $upc = $u['upc'];
    //        $listing_id = $u['store_listing_id'];
    //
    //        $result = $bigcommerce->update_upc($sku, $upc);
    //        $response = $bcinv->update_bc_upc($listing_id, $upc, $bc_username, $bc_api_key);
    //        print_r($response);
    //
    //        echo '<br>';
    //    }

    //    $sku = 'TA4208-48'; //G1-3  TA4208-48
    //    $quantity = 7;
    //    $minimumProfitPercent = 28;
    //    $minimumNetProfitPercent = 16;
    //    $increment = .20;
    //    $sku_id = $ecommerce->sku_soi($sku);
    //    $prices = $ecommerce->get_costs($sku_id);
    ////    print_r($prices);
    //    $msrp = $prices['msrp'];
    //    $pl10 = $prices['pl10'];
    //    $pl1 = $prices['pl1'];
    //    $cost = $prices['cost'];
    //    echo "$sku: <br>";
    ////    echo "Cost: $cost<br>";
    ////    echo "MSRP: $msrp<br>";
    ////    echo "PL10: $pl10<br>";
    ////    echo "PL1: $pl1<br>";
    //    $html = "<table class='tableBorder'>
    //    <thead><tr><th>MSRP</th><th>PL10</th><th>PL1</th><th>Cost</th></tr></thead>
    //    <tbody><tr><td>$msrp</td><td>$pl10</td><td>$pl1</td><td>$cost</td></tr></tbody>
    //</table><br><br>";
    //
    //    $shippingIncludedInPrice = 1; // 0 = No, Collected Shipping separately; 1 = Yes, Free Shipping to customer
    //    $priceArray = $ebay->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice);
    ////    print_r($priceArray);
    ////    echo "<br><br>";
    //    $totalPrice = $priceArray['totalPrice'];
    //    $totalCost = $priceArray['totalCost'];
    //
    //    $shippingCollected = $priceArray['shippingCollected']; //Amount we collected to ship the product
    //    $totalShipping = $priceArray['totalShipping'];
    //    $shippingCost = $priceArray['shippingCost']; //Amount we paid to ship the product
    //
    //    $ebayFeePercent = $priceArray['ebayFeePercent'];
    //    $ebayFeeMax = $priceArray['ebayFeeMax'];
    //    $ebayTotalFee = $priceArray['ebayTotalFee'];
    //
    //    $paypalFeePercent = $priceArray['paypalFeePercent'];
    //    $paypalFeeFlat = $priceArray['paypalFeeFlat'];
    //    $paypalTotalFee = $priceArray['paypalTotalFee'];
    //
    //    $minimumProfitPercent = $priceArray['minimumProfitPercent'];
    //
    //    $totalFees = $priceArray['totalFees'];
    //
    //    $grossProfit = $priceArray['grossProfit'];
    //    $grossProfitPercent = $priceArray['grossProfitPercent'];
    //
    //    $netProfit = $priceArray['netProfit'];
    //    $netProfitPercent = $priceArray['netProfitPercent'];
    //
    //    $html .= "Current Pricing Model<table class='tableBorder'>
    //    <thead><tr><th>Quantity</th><th>Price</th><th>Total Price</th><th>Shipping</th><th>Total Shipping</th><th>ShippingCost</th><th>eBay Fee</th><th>PayPal Fee</th><th>Cost</th><th>Total Cost</th><th>Gross Profit</th><th>Net Profit/Loss</th></tr></thead>
    //    <tbody>
    //        <tr>
    //            <td>$quantity</td>
    //            <td>$pl10</td>
    //            <td>$totalPrice</td>
    //            <td>$shippingCollected</td>
    //            <td>$totalShipping</td>
    //            <td>$shippingCost</td>
    //            <td>$ebayTotalFee</td>
    //            <td>$paypalTotalFee</td>
    //            <td>$cost</td>
    //            <td>$totalCost</td>
    //            <td>$grossProfit ($grossProfitPercent%)</td>
    //            <td>$netProfit ($netProfitPercent%)</td>
    //        </tr>
    //    </tbody>
    //    </table>";
    ////    echo $html;
    //    if($grossProfitPercent < $minimumProfitPercent || $netProfit < $minimumNetProfitPercent){
    //
    //        $priceArray = $ebay->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, 1);
    ////        echo "<br><br>";
    ////        print_r($priceArray);
    //
    //        $pl10 = $priceArray['pl10'];
    //        $totalPrice = $priceArray['totalPrice'];
    //        $totalCost = $priceArray['totalCost'];
    //
    //        $shippingCollected = $priceArray['shippingCollected']; //Amount we collected to ship the product
    //        $totalShipping = $priceArray['totalShipping'];
    //        $shippingCost = $priceArray['shippingCost']; //Amount we paid to ship the product
    //
    //        $ebayFeePercent = $priceArray['ebayFeePercent'];
    //        $ebayFeeMax = $priceArray['ebayFeeMax'];
    //        $ebayTotalFee = $priceArray['ebayTotalFee'];
    //
    //        $paypalFeePercent = $priceArray['paypalFeePercent'];
    //        $paypalFeeFlat = $priceArray['paypalFeeFlat'];
    //        $paypalTotalFee = $priceArray['paypalTotalFee'];
    //
    //        $minimumProfitPercent = $priceArray['minimumProfitPercent'];
    //
    //        $totalFees = $priceArray['totalFees'];
    //
    //        $grossProfit = $priceArray['grossProfit'];
    //        $grossProfitPercent = $priceArray['grossProfitPercent'];
    //
    //        $netProfit = $priceArray['netProfit'];
    //        $netProfitPercent = $priceArray['netProfitPercent'];
    //
    //        $html .= "Proposed Pricing Model to Raise Price<table class='tableBorder'>
    //        <thead><tr><th>Quantity</th><th>Price</th><th>Total Price</th><th>Shipping</th><th>Total Shipping</th><th>ShippingCost</th><th>eBay Fee</th><th>PayPal Fee</th><th>Cost</th><th>Total Cost</th><th>Gross Profit</th><th>Net Profit/Loss</th></tr></thead>
    //        <tbody>
    //            <tr>
    //                <td>$quantity</td>
    //                <td>$pl10</td>
    //                <td>$totalPrice</td>
    //                <td>$shippingCollected</td>
    //                <td>$totalShipping</td>
    //                <td>$shippingCost</td>
    //                <td>$ebayTotalFee</td>
    //                <td>$paypalTotalFee</td>
    //                <td>$cost</td>
    //                <td>$totalCost</td>
    //                <td>$grossProfit ($grossProfitPercent%)</td>
    //                <td>$netProfit ($netProfitPercent%)</td>
    //            </tr>
    //        </tbody>
    //        </table>";
    //    }elseif($grossProfitPercent > $minimumProfitPercent || $netProfit > $minimumNetProfitPercent){
    //
    //        $priceArray = $ebay->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, 1);
    ////        echo "<br><br>";
    ////        print_r($priceArray);
    //
    //        $pl10 = $priceArray['pl10'];
    //        $totalPrice = $priceArray['totalPrice'];
    //        $totalCost = $priceArray['totalCost'];
    //
    //        $shippingCollected = $priceArray['shippingCollected']; //Amount we collected to ship the product
    //        $totalShipping = $priceArray['totalShipping'];
    //        $shippingCost = $priceArray['shippingCost']; //Amount we paid to ship the product
    //
    //        $ebayFeePercent = $priceArray['ebayFeePercent'];
    //        $ebayFeeMax = $priceArray['ebayFeeMax'];
    //        $ebayTotalFee = $priceArray['ebayTotalFee'];
    //
    //        $paypalFeePercent = $priceArray['paypalFeePercent'];
    //        $paypalFeeFlat = $priceArray['paypalFeeFlat'];
    //        $paypalTotalFee = $priceArray['paypalTotalFee'];
    //
    //        $minimumProfitPercent = $priceArray['minimumProfitPercent'];
    //
    //        $totalFees = $priceArray['totalFees'];
    //
    //        $grossProfit = $priceArray['grossProfit'];
    //        $grossProfitPercent = $priceArray['grossProfitPercent'];
    //
    //        $netProfit = $priceArray['netProfit'];
    //        $netProfitPercent = $priceArray['netProfitPercent'];
    //
    //        $html .= "Proposed Pricing Model to Lower Price<table class='tableBorder'>
    //        <thead><tr><th>Quantity</th><th>Price</th><th>Total Price</th><th>Shipping</th><th>Total Shipping</th><th>ShippingCost</th><th>eBay Fee</th><th>PayPal Fee</th><th>Cost</th><th>Total Cost</th><th>Gross Profit</th><th>Net Profit/Loss</th></tr></thead>
    //        <tbody>
    //            <tr>
    //                <td>$quantity</td>
    //                <td>$pl10</td>
    //                <td>$totalPrice</td>
    //                <td>$shippingCollected</td>
    //                <td>$totalShipping</td>
    //                <td>$shippingCost</td>
    //                <td>$ebayTotalFee</td>
    //                <td>$paypalTotalFee</td>
    //                <td>$cost</td>
    //                <td>$totalCost</td>
    //                <td>$grossProfit ($grossProfitPercent%)</td>
    //                <td>$netProfit ($netProfitPercent%)</td>
    //            </tr>
    //        </tbody>
    //        </table>";
    //    }
    //
    //    echo $html;


    //    $searchTerm = '644153000212'; //644153000212
    //    $result = $aminv->find_existing_product($searchTerm);
    //    $result = str_replace("ns2:", "", $result);
    //    $parsed_xml = simplexml_load_string($result);
    //    print_r($result);
    ////    echo '<br><br>';
    ////    print_r($parsed_xml);
    //    echo '<br><br>';
    //    foreach($parsed_xml->ListMatchingProductsResult->Products->Product as $x){
    //        $asin = $x->Identifiers->MarketplaceASIN->ASIN;
    //        $salesCategory0 = $x->SalesRankings->SalesRank[0]->ProductCategoryId;
    //        $salesRank0 = $x->SalesRankings->SalesRank[0]->Rank;
    //        $salesCategory1 = $x->SalesRankings->SalesRank[1]->ProductCategoryId;
    //        $salesRank1 = $x->SalesRankings->SalesRank[1]->Rank;
    //        $attributes = $x->AttributeSets->ItemAttributes;
    //        $title = $attributes->Title;
    //        $brand = $attributes->Brand;
    //        $feature = $attributes->Feature;
    //        $listPrice = $attributes->ListPrice->Amount;
    //        $pictureUrl = $attributes->SmallImage->URL;
    //        $title = $attributes->Title;
    //        echo "$title - $asin - $listPrice;<br>$salesCategory0: $salesRank0; $salesCategory1: $salesRank1; <br><img src='$pictureUrl' /><br>";
    ////        var_dump(get_object_vars($attributes));
    ////        echo '<br>';
    //        $competitiveResult = $aminv->get_competitive_pricing_by_asin($asin);
    ////        print_r($competitiveResult);
    ////        echo '<br>';
    //        $parsedCompetitive = simplexml_load_string($competitiveResult);
    ////        print_r($parsedCompetitive);
    //        $competitivePricing = $parsedCompetitive->GetCompetitivePricingForASINResult->Product->CompetitivePricing;
    //        $cPricing = $competitivePricing->CompetitivePrices->CompetitivePrice->Price;
    //        $price = $cPricing->LandedPrice->Amount;
    //        $shipping = $cPricing->Shipping->Amount;
    //        $productsOffered = $competitivePricing->NumberOfOfferListings->OfferListingCount;
    //        $newProducts = $productsOffered[0];
    //        $usedProducts = $productsOffered[1];
    //        $refurbProducts = $productsOffered[2];
    //        $allProducts = $productsOffered[3];
    //        echo "Competitive Price/Shipping: $price/$shipping;<br>";
    //        echo "New: $newProducts; Used: $usedProducts; Refurbished: $refurbProducts; Total: $allProducts";
    //        echo '<br><br>';
    //    }
    //    $sku = 'AABM';
    //    $money = IBM::syncVAIPrices($sku, 1);
    //    $msrp = number_format($money[0]['J6LPRC'], 2);
    //    $pl1 = number_format($money[0]['J6PL01'], 2);
    //    $pl10 = number_format($money[0]['J6PL10'], 2);
    //    $cost = number_format($money[0]['U8COST'], 2);
    //    $sku_id = $ecommerce->sku_soi($sku);
    //    echo "$sku -> $msrp $pl10 $pl1 $cost";
    //    $result = $ecommerce->update_prices($sku_id, $msrp, $pl1, $pl10, $cost);
    //$ebinv->get_ebay_products($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $eb_store_id, $ecommerce);
    //    error_reporting(0);
    //    $results = $ecommerce->get_amazon_products(28500,2500); //, 12500,10000
    //    $amazon_base_url = 'http://www.amazon.com/gp/product/';
    //    $amazon_base_url_dp = 'http://www.amazon.com/dp/';
    //    $html = new DOMDocument("1.0");
    ////    print_r($results);
    //    foreach ($results as $r) {
    //        $sku = $r['sku'];
    //        $am_listing = $r['am_list'];
    //        $amazonhtml = $ecommerce->curl($amazon_base_url . $am_listing);
    ////        echo '<textarea>' . $amazonhtml . '</textarea>'. '<br><br><br>';
    ////        $html->loadHTMLFile($amazon_base_url . $am_listing);
    //        $html->loadHTML($amazonhtml);
    ////        print_r($html);
    //        $ul = $html->getElementById('wayfinding-breadcrumbs_feature_div');
    ////        print_r($ul);
    ////        echo '<br><br>';
    //        if(!empty($ul)) {
    //            $arr = $ul->getElementsByTagName("a");
    //            $parent_cat_id = '';
    //            $parent_cat = '';
    //            $product_category = '';
    //            foreach ($arr as $item) {
    //                $category_id = $item->getAttribute('href');
    //                $category_id = substr($category_id, strpos($category_id, '&node=') + 6);
    //                $category_name = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
    //                $cat_id = $ecommerce->save_category($category_id, $category_name, $parent_cat_id, 'categories_amazon');
    //                echo "$sku - $parent_cat ($parent_cat_id) > $category_name ($category_id)<br><br>";
    //                $parent_cat_id = $category_id;
    //                $parent_cat = $category_name;
    //                $product_category = $category_id;
    //            }
    //            echo "<br>";
    //        }else{
    //            $product_category = '4507';
    //        }
    //
    //        $result = $ecommerce->update_category($sku, $product_category, 'listing_amazon');
    //        if($result){
    //            echo "$sku has been updated with $product_category as the category<br>";
    //        }
    //    }

    //    $folder = '/var/www/html/portal/amazonimages/';
    //    $results = $ecommerce->get_products_from_all_channels(''); //, 15000, 10000
    //    $amazon_base_url = 'http://www.amazon.com/gp/product/';
    //    $amazon_base_url_dp = 'http://www.amazon.com/dp/';
    //
    //    $x = 1;
    //    echo count($results) . '<br>';
    //    $html = '<table><tr><td>SKU</td><td>Amazon</td><td>BigCommerce</td><td>eBay</td><td>Reverb</td></tr>';
    //    foreach ($results as $r) {
    //        $sku = $r['sku'];
    //        $filename = $sku;
    //
    //        $am_listing = $r['am_list'];
    //        $bc_listing = $r['bc_list'];
    //        $eb_listing = $r['eb_list'];
    //        $rev_listing = $r['rev_list'];
    //
    //        if (!file_exists($folder . $filename . '.jpg')) {
    //            $scraped_product = $ecommerce->curl($amazon_base_url . $am_listing);
    ////            echo '<textarea>' . $scraped_product . '</textarea>'. '<br><br><br>';
    //            if (strpos($scraped_product, '"hiRes":"http') === false && strpos($scraped_product, '"mainUrl":"') === false) {
    //                $scraped_product = $ecommerce->curl($amazon_base_url_dp . $am_listing);
    //                echo $sku . ' - DP listing' . '<br>';
    //            }
    //            if (!empty($scraped_product)) {
    //                $photo_url = '';
    //
    //                if (strpos($scraped_product, '"hiRes":"http') !== false) {
    //                    $photo_url = $ecommerce->substring_between($scraped_product, '"hiRes":"', '","thumb');
    //                } elseif (strpos($scraped_product, '"mainUrl":"') !== false) {
    //                    $photo_url = $ecommerce->substring_between($scraped_product, '"mainUrl":"', '","dimensions');
    //                } else {
    //                    $photo_url = $ecommerce->substring_between($scraped_product, 'data-a-dynamic-image="{&quot;', '&quot;:');
    //                    echo 'Different photo<br>';
    //                }
    //                echo $sku . ': ' . $photo_url . '<br>';
    //                $file = pathinfo($photo_url);
    //                $extension = $file['extension'];
    //                $filename = str_replace("/", "_", $filename); //*\/:?"<>|
    //                $filename = str_replace("*", "~", $filename);
    ////                $filename = str_replace(":", "", $filename);
    //                $image = $filename . '.' . $extension;
    //                if (!file_exists($folder . $filename . '.' . $extension)) {
    //                    copy($photo_url, $folder . $image);
    //
    //                    $top_description = urldecode($ecommerce->substring_between($scraped_product, "'encodedDescription' : \"", '",'));
    //                    $description = urldecode($ecommerce->substring_between($scraped_product, '2productDescriptionWrapper', '%0A%20%20%20%20%20%20%0A%20%20%20%20%20%20%3Cdiv%20class'));
    //                    if (empty($description)) {
    //                        $description = $top_description;
    //                    }
    //                    //    echo $top_description . '<br>';
    //                    //    echo $description . '<br>';
    //                    $rackspace_url = 'https://4dae45140096fd7fb6d3-7cac89ee19f3b4d177ef11effcca7827.ssl.cf1.rackcdn.com/images/';
    //                    $ecommerce->update_photo($sku, $rackspace_url . $image, 'listing_amazon');
    //                    $ecommerce->append_description($sku, $description, 'listing_amazon');
    //                }
    //                $html .= '<tr>';
    //                $html .= "<td>$x - $sku</td><td>$am_listing</td><td>$bc_listing</td><td>$eb_listing</td><td>$rev_listing</td><td><img class='channelimage' src='../../amazonimages/$image' /></td><td>$description</td>";
    //                $html .= '</tr>';
    //                $x++;
    //            }
    //        }
    ////      else{
    ////        $description = $ecommerce->get_description($sku, 'listing_amazon');
    ////        $image = $sku . '.jpg';
    ////    }
    //
    //    }
    //    $html .= '</table>';
    //    echo $html;


    //$ebad = new \ebad\ebadminclass($db);
    //$request = $ebad->get_ebay_categories($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token);
    ////    print_r($request);
    //$string = 'Connection: Keep-Alive';
    ////    $string = '<!--?xml version="1.0" encoding="UTF-8"?-->';
    //$xml = substr($request, strpos($request, $string)+strlen($string)+43);
    ////    echo '<br><br><br><br>';
    //$xml = simplexml_load_string($xml);
    ////    print_r($xml);
    ////echo '<br><br><br><br>';
    //$banned_categories = array(319,4953,166856,166857,166861,166805,4800,166813,
    //    166822,166827,166838);
    //foreach($xml->CategoryArray->Category as $cat){
    ////    print_r($cat);
    //    echo '<br>';
    //    $category_id = $cat->CategoryID;
    //    $category_name = $cat->CategoryName;
    //    $category_parent_id = $cat->CategoryParentID;
    //    $virtual = $cat->Virtual;
    //    if(in_array($category_id, $banned_categories) || in_array($category_parent_id, $banned_categories)){
    //        continue;
    //    }
    //    echo $category_id . ': ' . $category_name . ' - ' . $category_parent_id . '; Virtual: ' . $virtual;
    //    $parents = $ecommerce->get_category_to_map($category_parent_id);
    //    foreach($parents as $p){
    //        $p_cat_id = $p['id'];
    //        echo "Amazon Categories to look at: $p_cat_id<br>";
    //    }
    ////    $cat_id = $ecommerce->save_category($category_id, $category_name, $category_parent_id, 'categories_ebay');
    ////    if(!empty($cat_id)){
    ////        echo ' - Saved successfully';
    ////    }
    //    echo '<br><br><br><br>';
    //}

    //$sku = 'ZB';
    //    $sku2 = 'ZB2221/3';
    //    $sku_array = [$sku, $sku2];
    //$response = $aminv->get_fba_inventory($am_aws_access_key, $am_marketplace_id, $am_merchant_id, $am_secret_key, $sku_array);
    //print_r($response);

    //$updated = array("1003690","1003841","1003842","1003843","1004831","1005232","1009727","1009728","1009729","1009770","1010859","1011042","1011082","1011083","1011476","1011477","1011478","1011479","1011480","1011481","1011482","1011483","1011484","1011485","1011951","1013481","1013747","1014305","1014784","1014785","1016715","1018275","1019784","1019786","1019787","1019788","1019789","1019791","1020115","1020116","1020146","1021630","1021656","1023354","1023721","1023725","1023726","1023727","1030462","1030463","1030464","1030465","1030466","1030467","1030468","1032712","1033815","1034123","1035719","1036833","1040483","1040493","1040494","1041145","1041768","1043186","1043798","1043799","1043800","1043801","1044882","1046351","1046478","1048314","1049034","1049035","1049036","1049037","1049038","1051808","1051809","1051810","1051811","1054224","1054294","1054515","1054516","1054656","1054881","1054882","1054883","1054949","1055289","105531","1056111","1056406","1057138","1057757","1058390","1059088","1059333","1059493","1059989","1059991","1059993","1059994","1059995","1059998","1060002","1060007","1060008","1060014","1060016","1060016","1060022","1060028","1060029","1060191","1060192","1060436","1063953","1063954","1064137","1064627","1064628","1065576","1065577","1065578","1065921","1066144","1066145","1066145","1066146","1066162","1066163","1066164","1066165","1066166","1066167","1066168","1066169","1066170","1066171","1066833","1068118","1069615","1069924","1069976","1070074","1070218","1070715","1071143","1071144","1071145","1071146","1072165","1075293","1075472","1076084","1077997","1077998","1077999","1078000","1078001","1078001","1078002","1078004","1078005","1078006","1078007","1078008","1078010","1078011","1078012","1078013","1078013","1078014","1078015","1078016","1078017","1078018","1078019","1078020","1078021","1078022","1078023","1078024","1078025","1078026","1078027","1078265","1078913","1078913","1078914","1078915","1078916","1079009","1079011","1080201","1080829","1080830","1082302","1082467","1083362","1083363","1083364","1084869","1085890","1086384","1086385","1086389","1086393","1086949","1086950","1087195","1088290","1088425","1088617","1088830","1089521","1089974","1089975","1091851","1092815","1092945","1092956","1094150","1094183","1094184","1094281","1094584","1095647","1095648","1095661","1095662","1095663","1095664","1095665","1095666","1095667","1095668","1095840","1095867","1095868","1095875","1095876","1095877","1095877","1095878","1095878","1095879","1095879","1095994","1096003","1096004","1096005","1096007","1096008","1096009","1096071","1096093","1096094","1096095","1096096","1096098","1096099","1096101","1096102","1096106","1096106","1096109","1096203","1096203","1096206","1096206","1096210","1096211","1096265","1096267","1096295","1096296","1096297","1096301","1096403","1096513","1096517","1097532","1098092","1098093","1098094","1098095","1098151","1098267","1098268","1098269","1098368","1098369","1098383","1098395","1098396","1098397","1098398","1098399","1098400","1098419","1098841","1098975","1102166","1102166","1102167","1102168","1102169","1102170","1102171","1102172","1102173","1102174","1102530","1103037","1103305","1103577","1104987","1104993","1105995","1106945","1107053","1107054","1107445","1107446","1108289","1108326","1108327","1108636","1109577","1110046","1110047","1111532","1111533","1111534","1111535","1111662","1112205","1112770","1114922","1114923","1114924","1114925","1114926","1114927","1115082","1115349","1115658","1115659","1115660","1115661","1115662","1115663","1115664","1115665","1115666","1115667","1115668","1115669","1115670","1115671","1115672","1115673","1115674","1115675","1115676","1115676","1115676","1115677","1115677","1115678","1115679","1115680","1115681","1115682","1115683","1115684","1115685","1115686","1115687","1115688","1115689","1115690","1115691","1115692","1115693","1115694","1115695","1115696","1115697","1115698","1115699","1115700","1115701","1115702","1116724","1117028","1117092","1117093","1117094","1117095","1117747","1118027","1118105","1118106","114943","123805","125066","129379","129382","129384","129385","134588","141269","141270","141271","141272","141273","141274","141275","141281","141282","141283","141284","141285","141286","141287","141288","141289","141290","141291","141357","141358","141359","141359","141376","141377","141378","141379","141380","141381","141382","141383","141384","141385","141386","141387","141501","141653","143998","143999","144000","144001","144828","144829","144831","147091","147134","147135","147136","147137","148225","151413","151414","151415","151416","151417","155758","159537","159540","159541","159542","161020","161021","167054","169680","172078","172502","172750","172768","172769","172887","172888","175624","176792","178119","178588","178589","178590","180441","182679","182680","182681","184250","184251","184497","184498","184499","184603","187845","189172","189579","189580","189581","189582","189583","189584","189585","189586","189587","189588","189589","189590","189591","189592","189593","189594","189595","189596","189597","189598","189599","189600","189601","189602","189603","189607","189608","189609","189610","189611","189612","189613","189614","189615","189616","189977","191990","191991","191992","191993","191994","191996","191997","194620","194622","194794","194795","194796","194797","194798","194799","194800","194801","194802","197062","197063","197064","197065","197066","197067","197068","198679","198680","198681","198682","198683","198684","198685","198686","199217","202184","202185","202186","202187","202188","202189","202190","202191","202192","203878","203879","203880","203881","203882","203883","205628","209116","211861","211862","211863","211864","211865","212375","212376","215774","218195","218197","218455","218456","218768","218769","218770","218771","218772","218773","219846","219847","219848","219920","227309","227315","235003","235008","235017","235020","235021","235022","235023","235024","235025","235026","235026","235027","235028","235029","235030","236216","240740","247585","247586","247593","247594","247889","247890","247891","247892","250405","250560","253656","256381","256729","256731","34151","37529","43190","52049","65781","74807","74824","74894","91055","97670","99807");
    //$updated = array("1006059");
    //$updated = $ecommerce->get_inventory_for_update('listing_amazon', '1114946');
    //print_r($updated);
    // /* Update Amazon Quantity per SKU */
    //$x = 1;
    //$stock_id = $updated['id'];
    //$sku_id = $updated['sku_id'];
    //$stock_qty = $updated['stock_qty'];
    //$sku = $ecommerce->get_sku($sku_id);

    //Create XML for Amazon
    //$amazon_xml = AmazonInventory::inventoryArray($sku, $stock_qty, $x);

    //$x++;
    //Push to Amazon
    //echo $amazon_xml;
    //$response = $aminv->update_amazon_inventory($am_aws_access_key, $am_marketplace_id, $am_merchant_id, $am_secret_key, $amazon_xml);
    //print_r($response);

    // /* Update Amazon Prices */
    //$x = 1;
    //$amazon_price_xml = [];
    //foreach($updated as $u){
    //    $sku = $u;
    //    echo $sku . '<br>';
    //    $price = $ecommerce->get_inventory_price($sku, 'listing_amazon');
    //    if(!empty($price)){
    //        $amazon_price_xml = array_merge($amazon_price_xml, $amazon->create_inventory_price_update_item_xml($sku, $price, $x));
    //        $x++;
    //    }
    //}
    //
    //print_r($amazon_price_xml);
    //$response = $amazon->update_amazon_inventory_price($am_aws_access_key, $am_marketplace_id, $am_merchant_id, $am_secret_key, $amazon_price_xml);
    //print_r($response);
    //echo '<br><br>';

    //$results = $ecommerce->analyze_sales('');
    ////print_r($results);
    //$sold = array();
    //$z = 0;
    //foreach($results as $r){
    ////    if($z > 25){
    ////        continue;
    ////    }
    //    $sku = $r['sku'];
    //    $order_id = $r['id'];
    //    $channel = $r['name'];
    //    $date = date_create($r['date']);
    //    $date = $date->format('m/d/Y');
    //    $soldPrice = $r['price'];
    //    $shipping = $r['shipping_amount'];
    //    $quantitySold = $r['quantity'];
    //    $currentPrice = $r['current_price'];
    //    $sold[$sku][$date][$channel][$order_id]['soldPrice'] = $soldPrice;
    //    $sold[$sku][$date][$channel][$order_id]['shipping'] = $shipping;
    //    $sold[$sku][$date][$channel][$order_id]['total'] = $soldPrice + $shipping;
    //    $sold[$sku][$date][$channel][$order_id]['quantity'] = $quantitySold;
    //    $sold[$sku][$date][$channel][$order_id]['currentPrice'] = $currentPrice;
    //    $sold[$sku][$date][$channel][$order_id]['currentShipping'] = 3.99;
    //    $sold[$sku][$date][$channel][$order_id]['currentTotal'] = $currentPrice + 3.99;
    //    $z++;
    //}
    ////print_r($sold);
    //echo '<br><br>';
    //$differencetable = "<table>";
    //$html = "<table>";
    //foreach($sold as $key => $value){
    //    $sku = $key;
    //    $html .= "<tr>";
    //    $html .= "<td>$sku</td>";
    //    $html .= "<td><table>";
    //    $current_price = '';
    //    $current_shipping = '';
    //    $current_total = '';
    //    $channel = '';
    //    $total = '';
    //    foreach($value as $key2 => $value2){
    //        $date = $key2;
    //        $html .= "<tr><td>$date</td>";
    //        $html .= "<td colspan='3'><table>";
    //        foreach($value2 as $key3 => $value3){
    //            $channel = $key3;
    //            $html .= "<tr>";
    //            $html .= "<td>$channel</td>";
    //            $html .= "<td><table>";
    //            $html .= "<tr>";
    //            $html .= "<td>Order ID</td>";
    //            $html .= "<td>Sold Price</td>";
    //            $html .= "<td>Shipping</td>";
    //            $html .= "<td>Total</td>";
    //            $html .= "<td>Quantity Sold</td>";
    //            $html .= "<td>Difference</td>";
    //            $html .= "</tr>";
    //            foreach($value3 as $key4 => $value4){
    //                $order = $key4;
    //                $sold_price = $value4['soldPrice'];
    //                $shipping = $value4['shipping'];
    //                $total = $value4['total'];
    //                $quantity = $value4['quantity'];
    //                $current_price = $value4['currentPrice'];
    //                $current_shipping = $value4['currentShipping'];
    //                $current_total = $value4['currentTotal'];
    //                $difference = number_format($current_total - $total, 2);
    //                $html .= "<tr>";
    //                $html .= "<td>$order</td>";
    //                $html .= "<td>$sold_price</td>";
    //                $html .= "<td>$shipping</td>";
    //                $html .= "<td>$total</td>";
    //                $html .= "<td>$quantity</td>";
    //                $html .= "<td class='";
    //                if($difference <= 0){
    //                    $html .= "fontred";
    //                }else{
    //                    $html .= "fontgreen";
    //                }
    //                $html .= "'>$difference</td>";
    //                $html .= "</tr>";
    //            }
    //            $html .= "</table></td>";
    //            $html .= "</tr>";
    //        }
    //        $html .= "</table></td>";
    //        $html .= "</tr>";
    //    }
    //    $html .= "<tr><td></td>";
    //    $html .= "<td>Current Price</td>";
    //    $html .= "<td>Shipping</td>";
    //    $html .= "<td>Total</td>";
    //    $html .= "</tr>";
    //    $html .= "<td></td>";
    //    $html .= "<td>$current_price</td>";
    //    $html .= "<td>$current_shipping</td>";
    //    $html .= "<td>$current_total</td>";
    //    $html .= "</tr>";
    //    $html .= "</table></td>";
    //    $html .= "</tr>";
    //    if($channel == 'Ebay' && $difference > 0){
    //        $differencetable .= "<tr><td>$sku</td><td>$total</td></tr>";
    ////        $result = $ecommerce->update_price($sku, $total, 'listing_ebay');
    ////        $result = $ecommerce->update_override($sku, '1', 'listing_ebay');
    ////        $item_id = $ebay->get_listing_id($sku);
    ////        $response = $ebay->update_all_ebay_inventory($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $item_id, $total);
    ////        print_r($response);
    //    }
    //}
    //$html .= "</table>";
    //echo $html;
    //$differencetable .="</table>";
    //echo '<br><br>';
    //echo $differencetable


    //$files = scandir($dir);
    ////$z = 0;
    //foreach($files as $f){
    ////    if($z > 3){
    ////        continue;
    ////    }
    //    $name = $f;
    //    $order = substr($name, 0, -4);
    ////    print_r($f);
    //    if($order) {
    //        $file = file_get_contents($dir . $name);
    //        $items = substr_count($file, '<Item>');
    //        $sku = $ecommerce->substring_between($file,'<ItemId>','</ItemId>');
    //        $quantity = $ecommerce->substring_between($file,'<Qty>','</Qty>');
    //        $return = $ecommerce->update_item_qty($order, $sku, $quantity);
    //        if ($return) {
    //            echo $order . ': ' . $sku . ', ' . $quantity . ' - Updated Successfully.<br>';
    //        }
    //        if($items > 1){
    //            $itemIdClosingPos = strpos($file, '</Item>');
    //            $editedfile = substr($file, $itemIdClosingPos);
    //            for($x = 1; $x < $items; $x++){
    ////                echo $itemIdClosingPos . '<br>';
    ////                echo $editedfile . '<br>';
    //                $sku = $ecommerce->substring_between($editedfile,'<ItemId>','</ItemId>');
    //                if($sku == 'SALES TAX IDAHO @ 6%'){
    //                    $itemIdClosingPos = strpos($editedfile, '</Item>');
    //                    $editedfile = substr($editedfile, $itemIdClosingPos);
    //                    continue;
    //                }
    //                $quantity = $ecommerce->substring_between($editedfile,'<Qty>','</Qty>');
    //                $return = $ecommerce->update_item_qty($order, $sku, $quantity);
    //                if ($return) {
    //                    echo $order . ': ' . $sku . ', ' . $quantity . ' - Updated Successfully.<br>';
    //                }
    //                $itemIdClosingPos = strpos($editedfile, '</Item>', 10);
    //                $editedfile = substr($editedfile, $itemIdClosingPos);
    ////                echo '<br><br>';
    //            }
    //        }
    //    }
    //    echo '<br>';
    ////    $z++;
    //}

    //$results = $ebay->get_listings();
    //print_r($results);
    //$x = 1;
    //foreach($results as $r){
    ////    if($x > 2){
    ////        break;
    ////    }
    //    $listing_id = $r['store_listing_id'];
    //    $price = $r['price'];
    //    echo $listing_id . ': ' . $price . '<br>';
    //    $response = $ebinv->update_all_ebay_inventory($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $listing_id, $price);
    //    print_r($response);
    //    echo '<br><br>';
    //    $x++;
    //}

    //$results = $ebay->get_listings();
    //foreach($results as $r){
    //    echo $r['store_listing_id'] . ' ' . $r['id'] . '<br>';
    //}


    //$revinv->get_reverb_products($reverb_auth, $reverb_store_id, $ecommerce);

    //$bigcommerce->get_bc_products($BC, $bc_store_id, $ecommerce);


    //$channel = 'Amazon';
    //$accounts = $ecommerce->get_acct_num($channel);
    //print_r($accounts);
    //$co_one_acct = $accounts['co_one_acct'];
    //$co_two_acct = $accounts['co_two_acct'];
    //echo '<br>';
    //echo "CO1: $co_one_acct<br>CO2: $co_two_acct<br><br>";
    //$inventory = IBM::findInventory('500LG', $channel);
    //print_r($inventory);
    //echo '<br>';
    //$co_one_qty = $inventory['CO_ONE'];
    //$co_two_qty = $inventory['CO_TWO'];
    //echo "CO1 QTY: $co_one_qty<br>CO2 QTY: $co_two_qty<br>";
    //
    //if(empty($co_one_qty)){
    //    $channel_num = $co_two_acct;
    //}else{
    //    $channel_num = $co_one_acct;
    //}
    //echo $channel_num;

    //$request = curl_init('https://api.ebay.com/ws/api.dll');
    /*$post_string = '<?xml version="1.0" encoding="utf-8"?>
                <GetMyeBaySellingRequest  xmlns="urn:ebay:apis:eBLBaseComponents">
                    <RequesterCredentials>
                        <eBayAuthToken>' . $eb_token . '</eBayAuthToken>
                    </RequesterCredentials>
                    <ActiveList>
                        <Pagination>
                            <EntriesPerPage>5</EntriesPerPage>
                            <PageNumber>1</PageNumber>
                        </Pagination>
                    </ActiveList>
                </GetMyeBaySellingRequest>';*/

    //$headers = array(
    //    "Content-type: text/xml",
    //    "Content-length: " . strlen($post_string),
    //    "Connection: close",
    //    "X-EBAY-API-COMPATIBILITY-LEVEL: 967", //Only good until February 2016 - 889
    //    "X-EBAY-API-DEV-NAME: $eb_dev_id",
    //    "X-EBAY-API-APP-NAME: $eb_app_id",
    //    "X-EBAY-API-CERT-NAME: $eb_cert_id",
    //    "X-EBAY-API-CALL-NAME: GetMyeBaySelling",
    //    "X-EBAY-API-SITEID: 0"
    //);
    //curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    //curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($request, CURLOPT_HEADER, 1);
    //curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
    //curl_setopt($request, CURLOPT_POST, 1);
    //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
    //$response = curl_exec($request);
    ////print_r($response);
    //curl_close ($request);
    //$listing_array = $ecommerce->get_items($response);
    //print_r($listing_array);
    //echo '<br><br>';
    //foreach($listing_array as $key => $val){
    //    $item_id = $ecommerce->substring_between($val, '<itemid>', '</itemid>');
    //    echo $item_id . '<br>';
    //    $response = $ebay->getSingleItem($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $item_id);
    //    print_r($response);
    //    $country = $ecommerce->substring_between($response, '<country>', '</country>');
    //    $description = $ecommerce->substring_between($response, '<description>', '</description>');
    //    $price = $ecommerce->substring_between($response, '<startprice>', '</startprice>');
    //    $url = $ecommerce->substring_between($response, '<viewitemurl>', '</viewitemurl>');
    //    $listing_duration = $ecommerce->substring_between($response, '<listingduration>', '</listingduration>');
    //    $listing_type = $ecommerce->substring_between($response, '<listingtype>', '</listingtype>');
    //    $primary_category = $ecommerce->substring_between($response, '<categoryid>', '</categoryid>');
    //    $category_name = $ecommerce->substring_between($response, '<categoryname>', '</categoryname>');
    //    $quantity = $ecommerce->substring_between($response, '<quantity>', '</quantity>');
    //    $isbn = $ecommerce->substring_between($response, '<isbn>', '</isbn>');
    //    $stock_photo = $ecommerce->substring_between($response, '<stockphotourl>', '</stockphotourl>');
    //    $global_shipping = $ecommerce->substring_between($response, '<globalshipping>', '</globalshipping>');
    //    $free_shipping = $ecommerce->substring_between($response, '<freeshipping>', '</freeshipping>');
    //    $shipping_cost = $ecommerce->substring_between($response, '<shippingservicecost>', '</shippingservicecost>');
    //    $shipping_cost_additional = $ecommerce->substring_between($response, '<shippingserviceadditionalcost>', '</shippingserviceadditionalcost>');
    //    $shipping_type = $ecommerce->substring_between($response, '<shippingtype>', '</shippingtype>');
    //    $title = $ecommerce->substring_between($response, '<title>', '</title>');
    //    $sku = $ecommerce->substring_between($response, '<sku>', '</sku>');
    //    $photo_url = $ecommerce->substring_between($response, '<galleryurl>', '</galleryurl>');
    //    $external_photo_url = $ecommerce->substring_between($response, '<externalpictureurl>', '</externalpictureurl>');
    //    $refund_option = $ecommerce->substring_between($response, '<refundoption>', '</refundoption>');
    //    $returnswithinoption = $ecommerce->substring_between($response, '<returnswithinoption>', '</returnswithinoption>');
    //    $returnsacceptedoption = $ecommerce->substring_between($response, '<returnsacceptedoption>', '</returnsacceptedoption>');
    //    $return_description = $ecommerce->substring_between($response, '</retunsaccepted><description>', '</description><shippingcostpaidyoption></shippingcostpaidyoption>');
    //    echo '<br><br>';
    //}


    //$results = $reverb->get_reverb_app_id($user_id);
    //$reverb_email = $results['reverb_email'];
    //$reverb_password = $crypt->decrypt($results['reverb_pass']);
    //$rev_store_id = $results['store_id'];
    //
    //$request = $reverb->get_auth($reverb_email, $reverb_password);
    //$token = $ecommerce->substring_between($request,'"token":"','","paypal_email');
    //$request = $reverb->update_reverb_tracking($token, '683086', '9400110200829758630290', 'USPS', "false");
    //print_r($request);

    //for($page = 1; $page < 2; $page++) { //340
    //    $request = $reverb->get_reverb_listings($token, $page);
    //    $listings = substr($request, strpos($request, '"listings":'), -1);
    //    $listings = '{' . $listings . '}';
    //    $listings = json_decode($listings);
    //    $x = 0;
    //    foreach ($listings as $listing) {
    //        foreach ($listing as $r) {
    //            print_r($r);
    //            $make = $r->make;
    //            $sku = $r->sku;
    //            $model = $r->model;
    //            $finish = $r->finish;
    //            $title = $r->title;
    //            $created_at = $r->created_at;
    //            $shop_name = $r->shop_name;
    //            $description = $r->description;
    //            $condition = $r->condition;
    //            $price_obj = $r->price;
    //            $amount = $r->price->amount;
    //            $symbol = $r->price->symbol;
    //            $offers_enabled = $r->offers_enabled;
    //            $inventory = $r->inventory;
    //            $links_obj = $r->_links;
    //            $photo_url = $r->_links->photo->href;
    //            $shipping_obj = $r->shipping;
    //            $local = $r->shipping->local;
    //            $us = $r->shipping->us;
    //            $us_rate_obj = $r->shipping->us_rate;
    //            $ship_amount = $r->shipping->us_rate->amount;
    //            $currency = $r->shipping->us_rate->currency;
    //            $photo_array = $r->photos;
    //            $large_photo_url = $r->photos[0]->_links->large_crop;
    //            $small_photo_url = $r->photos[0]->_links->small_crop;
    //            $full_photo_url = $r->photos[0]->_links->full;
    //            $thumbnail_photo_url = $r->photos[0]->_links->thumbnail;
    ////            $sku = $l->sku;
    ////            $condition = $l->condition;
    ////            if($condition == 'Brand New'){
    ////                $condition = 'New';
    ////            }
    ////            $title = $l->title;
    ////            $description = $l->description;
    ////            $price = $l->price->amount;
    ////            $url = $l->_links->web->href;
    ////            echo 'Title: ' . $title . '<br>';
    ////            echo 'Description: ' . $description . '<br>';
    ////            echo 'Price: ' . $price . '<br>';
    ////            echo 'URL: ' . $url . '<br>';
    ////            $product_id = $ecommerce->product_soi($sku, $title, '', $description, '', '');
    ////            echo 'Product ID: ' . $product_id . '<br>';
    ////            $ecommerce->availability_soi($product_id, 4);
    ////            $sku_id = $ecommerce->sku_soi($sku);
    ////            $condition_id = $ecommerce->condition_soi($condition);
    ////            $stock_id = $ecommerce->stock_soi($sku_id, $condition_id);
    ////            $listing_array = array(
    ////
    //
    //             );
    ////            $listing_id = $ecommerce->listing_soi('listing_reverb', $rev_store_id, $stock_id, $sku, $url, $title, $description);
    ////            $x++;
    //            echo '<br><br>';
    //        }
    //    }
    //}

    //$request = $reverb->get_orders($token, $reverb_email, $reverb_password);
    //$orders = substr($request, strpos($request, '"orders":'), -1);
    //$orders = '{' . $orders . '}';
    //$orders = json_decode($orders);
    //foreach($orders as $o){
    //    foreach($o as $order) {
    //        print_r($order);
    //        echo '<br><br>';
    //        $ship_to_name = $order->buyer_name;
    //        $name = explode(' ',$ship_to_name);
    //        $last_name = ucwords(strtolower(array_pop($name)));
    //        $first_name = ucwords(strtolower(implode(' ',$name)));
    //        $state = $order->shipping_address->region;
    //        $zip = $order->shipping_address->postal_code;
    //        $city = $order->shipping_address->locality;
    //        $address = $order->shipping_address->street_address;
    //        $address2 = $order->shipping_address->extended_address;
    //        $buyer_phone = $order->shipping_address->phone;
    //        $country = $order->shipping_address->country_code;
    //        if ($country == 'US') {
    //            $country = 'USA';
    //        }
    //        $order_num = $order->order_number;
    //        $timestamp = $order->created_at;
    //        $order_date = $timestamp;
    //        $shipping = 'ZSTD';
    //        $sku = $order->sku;
    //        $title = $order->title;
    //        $quantity = $order->quantity;
    //        $upc = '';
    //        $principle = $order->amount_product_subtotal->amount;
    //        $shipping_amount = $order->shipping->amount;
    //        $ponumber = 1;
    //        $channel_num = '5001942';
    //        $channel_name = 'Reverb';
    //        if (strcasecmp($state, 'ID') == 0) {
    //            //Subtract 6% from sub-total, add as sales tax; adjust sub-total
    //            $tax = $principle * .06;
    //            $principle -= $tax;
    //        }
    //        $item_xml = $ecommerce->create_item_xml($sku, $title, $ponumber, $quantity, $principle, $upc);
    //        if (strcasecmp($state, 'ID') == 0) {
    //            $ponumber++;
    //            $item_xml .= $ecommerce->create_tax_item_xml($ponumber, $tax);
    //        }
    //        $total = $principle;
    //        $state_id = $ecommerce->state_soi($state);
    //        $zip_id = $ecommerce->zip_soi($zip, $state_id);
    //        $city_id = $ecommerce->city_soi($city, $state_id);
    //        $cust_id = $ecommerce->customer_soi($first_name, $last_name, ucwords(strtolower($address)),ucwords(strtolower($address2)),$city_id,$state_id,$zip_id);
    //        $order_id = $ecommerce->save_order($rev_store_id, $cust_id, $order_num, $shipping);
    //        $sku_id = $ecommerce->sku_soi($sku);
    //        $ecommerce->save_order_items($order_id, $sku_id, $total);
    //        $xml = $ecommerce->create_xml($channel_num, $channel_name, $order_num, $timestamp, $shipping_amount, $shipping, $order_date, $buyer_phone, $ship_to_name, $address, $address2, $city, $state, $zip, $country, $item_xml);
    //        $ecommerce->saveXmlToFTP($order_num, $xml, $folder, $channelName);
    //    }
    //}
    //$name = $ecommerce->substring_between($request, '"name":"', '","street_address"');
    //$state = $ecommerce->substring_between($request, '"region":"', '","postal_code"');
    //$zip = $ecommerce->substring_between($request, '"postal_code":"', '","country_code"');
    //$city = $ecommerce->substring_between($request, '"locality":"', '","region"');
    //$order_num = $ecommerce->substring_between($request, '"order_number":"', '","needs_feedback_for_buyer"');
    //$city = $ecommerce->substring_between($request, '', '');
    //echo '<br><br>';
    //echo $name . '<br>';


    //echo $eb_dev_id . "<br>";
    //echo $eb_app_id . "<br>";
    //echo $eb_cert_id . "<br>";
    //echo $eb_token . "<br><br>";
    //$orders_last_day = $ecommerce->get_orders_in_last_day();
    //print_r($orders_last_day);

    //foreach($orders_last_day as $o){
    //    $order_num = $o['order_id'];
    //    $order_id = $ecommerce->get_order_id($order_num);
    //    $channel = $o['type'];
    //    $item_id = $o['item_id'];
    //    $trans_id = '';
    //    if(!empty($item_id)){
    //        $num_id = explode('-',$item_id);
    //        $item_id = $num_id[0];
    //        $trans_id = $num_id[1];
    //    }
    //    $carrier = '';
    //$order_num = '1778';
    //    $tracking_id = trim(IBM::get_similar_tracking_num($order_num));
    //    echo $order_num . '-> ';
    //    if(empty($tracking_id)){
    //        $tracking_id = trim(IBM::get_ups_tracking_num($order_num));
    //        $carrier = 'UPS';
    //    }else{
    //        $carrier = 'USPS';
    //    }
    //echo $tracking_id;
    //    if(!empty($tracking_id)) {
    //        echo $order_id . ': ' . $tracking_id . '; Channel: ' . $channel . '<br>';
    //        $result = $ecommerce->update_tracking_num($order_id, $tracking_id, $carrier);
    //        echo $result . '<br>';
    //        if ($channel == 'BigCommerce') {
    //            //update BC
    //            $response = $bigcommerce->update_bc_tracking($BC, $bc_username, $bc_api_key, $order_num, $tracking_id, $carrier);
    //            print_r($response);
    //            echo '<br>';
    //            if($response){
    //                $ecommerce->update_tracking_succesful($order_num);
    //                echo 'Tracking for MML order ' . $order_num . ' was updated!<br><br>';
    //            }
    //        } elseif ($channel == 'EBay') {
    //            //update Ebay
    //            $response = $ebay->update_ebay_tracking($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $tracking_id, $carrier, $item_id, $trans_id);
    ////            echo $item_id . '<br><br>';
    //            print_r($response);
    //            echo '<br>';
    //            if(strpos($response, 'Success')){
    //                $ecommerce->update_tracking_succesful($order_num);
    //                echo 'Tracking for eBay order ' . $order_num . ' was updated!<br><br>';
    //            }
    //        } elseif ($channel == 'Amazon') {
    //            //update Amazon
    //            $response = $amazon->update_amazon_tracking($am_aws_access_key, $am_marketplace_id, $am_merchant_id, $am_secret_key, $order_num, $tracking_id, $carrier);
    //            print_r($response);
    //            echo '<br>';
    //            if(strpos($response, 'SUBMITTED')) {
    //                $ecommerce->update_tracking_succesful($order_num);
    //                echo 'Tracking for Amazon order ' . $order_num . ' was updated!<br><br>';
    //            }
    //        }
    //    }
    //}

    //$bigcommerce->update_bc_tracking($BC, $order_id, $tracking_num);

    //$count = IBM::getVIOCount();
    //echo $count . '<br><br>';
    //$products = IBM::sample_inv();
    //foreach($products as $p){
    //    $sku = $p['ITITEM'];
    //    $mml_price = $p['ITMMLPRICE'];
    //    $mml_sku = $p['ITMMLITEM'];
    //    $mml_qty = $p['ITMMLQTY'];
    //    $am_price = $p['ITAMPRICE'];
    //    $am_sku = $p['ITAMITEM'];
    //    $am_qty = $p['ITAMQTY'];
    //    $eb_price = $p['ITAMPRICE'];
    //    $eb_sku = $p['ITEBITEM'];
    //    $eb_qty = $p['ITEBQTY'];
    //    echo $sku . ': <br>';
    //    echo '    ' . $mml_sku . ' - Price: ' . $mml_price . '; QTY: ' . $mml_qty . '<br>';
    //    echo '    ' . $am_sku . ' - Price: ' . $am_price . '; QTY: ' . $am_qty . '<br>';
    //    echo '    ' . $eb_sku . ' - Price: ' . $eb_price . '; QTY: ' . $eb_qty . '<br>';
    //    echo '<br><br>';
    //}


    //$vai->update_db();
    //global $ebayappid;
    //echo $ebayappid . "<br />";
    //$ebay->get_ebay_inventory($appid);
    //$ebay->update_ebay_inventory($appid);
    //$bigcommerce->configure($BC, $bc_store_url, $bc_username, $bc_api_key);
    //$ping = $BC::getTime();
    //if($ping){echo $ping->format('H:i:s');}else{echo 'no go';};
    //echo "<br />";
    //$filter = array(
    //    'min_date_created' => date('r', strtotime("-3 days")),
    //    'status_id' => 11
    //);
    //$bigcommerce->test_get_bc_orders($BC,$filter,$bc_username, $bc_api_key, $ecommerce);
    //$bigcommerce->get_bc_orders($BC,$filter,$bc_username, $bc_api_key, $ecommerce);

    //$count = $BC::getProductsCount();
    //echo $count . '<br />';
    //$x = 1;
    //for($pn = 1; $pn < 2; $pn++){ //$pn = 36
    //    $filter = array(
    //        "page" => $pn,
    //        "limit" => 4 //250
    //    );
    //    $products = $BC::getProducts($filter);
    //    foreach ($products as $p) {
    //        print_r($p);
    //        echo '<br><br>';
    //        $name = $p->name;
    //        $store_listing_id = $p->id;
    //        $sku = $p->sku;
    //        $price = $p->price;
    //        $condition = $p->condition;
    //        $description = $p->description;
    //        $inventory_level = $p->inventory_level;
    //        $type = $p->type;
    //        $search_keywords = $p->search_keywords;
    //        $keyword_filter = $p->keyword_filter;
    //        $cost_price = $p->cost_price;
    //        $retail_price = $p->retail_price;
    //        $sale_price = $p->sale_price;
    //        $calculated = $p->calculated_price;
    //        $sort_order = $p->sort_order;
    //        $visible = $p->is_visible;
    //        $featured = $p->is_featured;
    //        $related_products = $p->related_products;
    //        $inventory_warning_level = $p->inventory_warning_level;
    //        $warranty = $p->warranty;
    //        $width = $p->width;
    //        $weight = $p->weight;
    //        $height = $p->height;
    //        $depth = $p->depth;
    //        $meta_keywords = $p->meta_keywords;
    //        $meta_description = $p->meta_description;
    //        $page_title = $p->page_title;
    //        $url = 'https://mymusiclife.com' . $p->custom_url;
    //        $fixed_cost_shpping_price = $p->fixed_cost_shipping_price;
    //        $free_shipping = $p->is_free_shipping;
    //        $inventory_tracking = $p->inventory_tracking;
    //        $rating_total = $p->rating_total;
    //        $rating_count = $p->rating_count;
    //        $total_sold = $p->total_sold;
    //        $date_created = $p->date_created;
    //        $brand_id = $p->brand_id;
    //        $view_count = $p->view_count;
    //        $layout_file = $p->layout_file;
    //        $price_hidden = $p->is_price_hidden;
    //        $hidden_label = $p->is_hidden_label;
    //        $date_modified = $p->date_modified;
    //        $event_date_field_name = $p->event_date_field_name;
    //        $event_date_type = $p->event_date_type;
    //        $event_date_start = $p->event_date_start;
    //        $event_date_end = $p->event_date_end;
    //        $myob_asset_account = $p->myob_asset_account;
    //        $myob_income_account = $p->myob_income_account;
    //        $myob_expense_account = $p->myob_expense_account;
    //        $peachtree_gl_account = $p->peachtree_gl_account;
    //        $condition_shown = $p->is_condition_shown;
    //        $preorder_release_date = $p->preorder_release_date;
    //        $preorder_only = $p->is_preorder_only;
    //        $preorder_message = $p->preorder_message;
    //        $order_quantity_minimum = $p->order_quantity_minimum;
    //        $order_quantity_maximum = $p->order_quantity_maximum;
    //        $open_graph_type = $p->open_graph_type;
    //        $open_graph_title = $p->open_graph_title;
    //        $open_graph_description = $p->open_graph_description;
    //        $open_graph_thumbnail = $p->is_open_graph_thumbnail;
    //        $upc = $p->upc;
    //        $avalara_product_tax_code = $p->avalara_product_tax_code;
    //        $date_last_imported = $p->date_last_imported;
    //        $option_set_id = $p->option_set_id;
    //        $tax_class_id = $p->tax_class_id;
    //        $option_set_display = $p->option_set_display;
    //        $bin_picking_number = $p->bin_picking_number;
    //        $custom_url = $p->custom_url;
    //        $availability = $p->availability;
    //        $brand_json = $p->brand->url;
    //        $downloads_json = $p->downloads->url;
    //        $images_json = $p->images->url;
    //        $discount_rules_json = $p->discount_rules->url;
    //        $configurable_fields_json = $p->configurable_fields->url;
    //        $custom_fields_json = $p->custom_fields->url;
    //        $videos_json = $p->videos->url;
    //        $skus_json = $p->skus->url;
    //        $rules_json = $p->rules->url;
    //        $options_json = $p->options->url;
    //        $tax_class_json = $p->tax_class->url;
    ////        echo $name . ': ' . $url;
    //
    //
    ////        $result = $bigcommerce->save_bc_id($bc_store_id, $name, $store_listing_id, $sku, $condition, $description, $width, $weight, $height, $depth, $meta_keywords, $meta_description, $page_title, $price);
    ////        if($result) {
    ////            echo $x . ' -> Name: ' . $name . '<br />';
    ////            echo 'ID: ' . $store_listing_id . '<br />';
    ////            echo 'Condition: ' . $condition . '<br />';
    ////            echo 'SKU: ' . $sku . '<br /><br />';
    ////            echo "$name saved in db successfully.";
    ////        }
    //    }
    //}


    //$filter = array("page" => 1, "limit" => 4);
    //$bigcommerce->get_bc_products_info($BC, $filter);
    //$produc
    //$products = $Bigcommerce->getProducts();
    //foreach($products as $product){
    //    echo $product->name;
    //    echo $product->price;
    //}
    //$posts = $db->query('SELECT account.id, first_name, COUNT(*) AS comments_count FROM account INNER JOIN store ON store.company_id = "1" ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    //print_r($posts);
}
?>
<?php
include 'footer-marketing.php';
?>
