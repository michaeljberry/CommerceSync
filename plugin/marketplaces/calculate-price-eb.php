<?php

use ecommerce\Ecommerce;

require '../../core/init.php';

require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

if ($_POST['price_sku']) {

    $sku = htmlentities($_POST['price_sku']);
    $quantity = htmlentities($_POST['price_quantity']);
    $minimumProfitPercent = htmlentities($_POST['price_margin']);
    $minimumNetProfitPercent = htmlentities($_POST['price_net_profit']);
    $increment = htmlentities($_POST['price_increment']);
    $shippingIncludedInPrice = htmlentities(isset($_POST['price-include-shipping']) ? $_POST['price-include-shipping'] : 0 );
    $shippingCharged = htmlentities($_POST['price_shipping']);

    $sku_id = $ecommerce->skuSoi($sku);
    $prices = $ecommerce->getSKUCosts($sku, 'listing_ebay');
    extract($prices);

    $priceVariables = compact(
        "sku", "quantity", "minimumProfitPercent", "minimumNetProfitPercent", "increment",
        "sku_id", "msrp", "pl10", "pl1", "cost", "override", "title", "upc"
    );
//    \ecommerceclass\ecommerceclass::dd($priceVariables);
    $currentEbayListings = simplexml_load_string($ebinv->findItemsAdvanced($upc));
    $currentAmazonListings = simplexml_load_string($aminv->getLowestOfferListingsForSKU($sku));
    $ourAmazonPrice = simplexml_load_string($aminv->GetMyPriceForSKU($sku));
    $ourSalesHistory = $ecommerce->getSalesHistory($sku_id);
//    $ebayRecentSales = simplexml_load_string($ebinv->findCompletedItems($upc));

    echo "$title: $upc-> $sku: <br>";
    $label = "VAI Pricing";
    $tableArray = [
        [
            'msrp' => $msrp,
            'pl10' => $pl10,
            'pl1' => $pl1,
            'cost' => $cost
        ]
    ];

    echo Ecommerce::arrayToTable($tableArray, $label);
    echo "<br><br>";

    if (!empty($override)) {
        $stock_id = $ecommerce->stockSoi($sku_id);
        $listing_id = $ecommerce->get_listing_id($stock_id, 'listing_ebay');
        $response = simplexml_load_string($ebinv->getSingleItem(listing_id));
        $pl10 = $response->Item->StartPrice;
    }

    $currentPriceArray = $ebay->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged);

    $label = "Current Pricing Model";
    $tableArray = $ebay->pricingTables($currentPriceArray);

    echo Ecommerce::arrayToTable($tableArray, $label);
    echo "<br><br>";

    $label = "Proposed Pricing Model to Adjust Price";
    $proposerdPriceArray = $ebay->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged, 1);
    $tableArray = $ebay->pricingTables($proposerdPriceArray);

    echo Ecommerce::arrayToTable($tableArray, $label);
    echo "<br><br>";

    $ebaySellers = $ebinv->sorteBaySearchResults($currentEbayListings);
    $ebaySellers = $ebinv->removeExtraSellerInfo($ebaySellers);

    $label = 'Current Listings on eBay for same SKU';
    $tableArray = [];

    foreach($ebaySellers as $s) {
        $tableArray[] = [
            'title' => [
                'value' => $s['title'],
                'url' => $s['url']
            ],
            'seller' => $s['seller'],
            'feedback' => $s['sellerFeedback'],
            'reviews' => $s['sellerScore'],
            'price' => $s['price'],
            'shippingCollected' => $s['shippingCollected'],
            'total' => $s['total']
        ];
    }

    echo Ecommerce::arrayToTable($tableArray, $label);

    $amazonPrice = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->ListingPrice->Amount;
    $amazonShipping = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->Shipping->Amount;
    $amazonTotal = $ourAmazonPrice->GetMyPriceForSKUResult->Product->Offers->Offer->BuyingPrice->LandedPrice->Amount;

    echo "<br><br>Our Amazon Listing - Price: $amazonPrice; Shipping: $amazonShipping; Total: $amazonTotal<br><br>";

    $amazonListings = $aminv->sortAmazonSearchResults($currentAmazonListings);
    $arrayToInclude = ['numOfListingsAtThisPrice', 'sellerRating', 'shippingTime', 'price', 'shipping', 'total'];
    $label = 'Current Listings on Amazon for same SKU';
    $tableArray = [];

    foreach($amazonListings as $a) {
        $tableArray[] = [
            'numOfListingsAtThisPrice' => $a['numOfListingsAtThisPrice'],
            'sellerRating' => $a['sellerRating'],
            'shippingTime' => $a['shippingTime'],
            'price' => $a['price'],
            'shippingCollected' => $a['shipping'],
            'total' => $a['total']
        ];
    }

    echo Ecommerce::arrayToTable($tableArray, $label);

    ?>
    <div id='ourSalesHistoryChart'></div><br><br>
    <div id='ebayRecentSalesChart'></div>

    <script type='text/javascript'>
        var groupLabels = [];
        graph('#ourSalesHistoryChart', groupLabels, '<?php echo RELPLUGIN; ?>marketplaces/product-stats.php?sku_id=<?php echo $sku_id; ?>', '', 'Sales $', '%Y-%m')
    </script>
    <?php
}