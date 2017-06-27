<?php
set_time_limit(3600);
error_reporting(-1);
include __DIR__ . '/../../core/init.php';
include_once WEBPLUGIN . 'am/amvar.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';

//define('AWS_ACCESS_KEY_ID', $aminv->am_aws_access_key);
//define('AWS_SECRET_ACCESS_KEY', $aminv->am_secret_key);
//define('MERCHANT_ID', $aminv->am_merchant_id);
//define('MARKETPLACE_ID', $aminv->am_marketplace_id);

$report = true;

echo 'Now: ' . date('m/d/y h:i:s') . '<br />';

include_once('/var/www/html/portal/plugin/am/MarketplaceWebService/Samples/.config.inc.php');

include_once('functions.php');

$serviceUrl = "https://mws.amazonservices.com";
$config = array(
    'ServiceURL' => $serviceUrl,
    'ProxyHost' => null,
    'ProxyPort' => -1,
    'MaxErrorRetry' => 3,
);
$service = new MarketplaceWebService_Client(
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY,
    $config,
    APPLICATION_NAME,
    APPLICATION_VERSION);

$report_request_id = '100818016919';

if (!$report) {
    echo '<br />';
    $parameters = array(
        'Marketplace' => MARKETPLACE_ID,
        'Merchant' => MERCHANT_ID,
        'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
    );
    echo '<br /><br/>Request Report Request:<br><br>';
    $request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
    print_r($request);
    invokeRequestReport($service, $request);
    echo '<br /><br/>';
} else {
    echo '<br />';
    $parameters = array(
        'Marketplace' => MARKETPLACE_ID,
        'Merchant' => MERCHANT_ID,
        'ReportRequestList' => $report_request_id,
    );

    echo '<br /><br/>Get Report Request List Request:<br><br>';
    $request = new MarketplaceWebService_Model_GetReportRequestListRequest($parameters);
    print_r($request);
    invokeGetReportRequestList($service, $request);
    $parameters = array(
        'Marketplace' => MARKETPLACE_ID,
        'Merchant' => MERCHANT_ID,
        'AvailableToDate' => new DateTime('now'),
        'AvailableFromDate' => new DateTime('-6 months'),
        'Acknowledged' => false,
    );

    $request = new MarketplaceWebService_Model_GetReportListRequest($parameters);
    echo '<br /><br/>';
    $report_id = invokeGetReportList($service, $request, true);
    echo 'Report ID: ' . $report_id . '<br><br>';
    $parameters = array(
        'Marketplace' => MARKETPLACE_ID,
        'Merchant' => MERCHANT_ID,
        'Report' => @fopen('php://memory', 'rw+'),
        'ReportId' => $report_id,
    );
    $report = new MarketplaceWebService_Model_GetReportRequest($parameters);
    $products = invokeGetReport($service, $report, true);
    echo '<br><br><br><br>';
    print_r($products);
    $am_products = explode(PHP_EOL, $products);
    echo '<br><br><br><br>';
    $x = 0;
    $y = count($am_products);
    echo 'Num of products' . $y . '<br><br>';
    foreach ($am_products as $p) {
        if ($x < $y) {
            if ($x == 0) {
                $x++;
                continue;
            }
            $p_col = preg_split("/[\t]/", $p);
            $item_name = $p_col[0];
            $name = substr($item_name, 0, strpos($item_name, '[') - 1);
            $description = $p_col[1];
            $store_listing_id = $p_col[2];
//            $id = $amazon->find_amazon_listing($store_listing_id);
//            if(!empty($id)){
//                continue;
//            }
            $sku = $p_col[3];
            $price = $p_col[4];
            $inventory_level = $p_col[5];
            $open_date = $p_col[6];
            $image_url = $p_col[7];
            $item_is_marketplace = $p_col[8];
            $product_id_type = $p_col[9];
            $zshop_shipping_fee = $p_col[10];
            $item_note = $p_col[11];
            $product_condition = $p_col[12];
            $zshop_category1 = $p_col[13];
            $zshop_browse_path = $p_col[14];
            $zshop_storefront_feature = $p_col[15];
            $asin1 = $p_col[16];
            $url = 'https://www.amazon.com/gp/product/' . $asin1;
            $asin2 = $p_col[17];
            $asin3 = $p_col[18];
            $will_ship_internationally = $p_col[19];
            $expedited_shipping = $p_col[20];
            $zshop_boldface = $p_col[21];
            $product_id = $p_col[22];
            $bid_for_featured_placement = $p_col[23];
            $add_delete = $p_col[24];
            $pending_quantity = $p_col[25];
            $fulfillment_channel = $p_col[26];
            if ($fulfillment_channel == 'DEFAULT') {
                $fulfillment_channel = 'SELLER';
            }

            //find-product-id
            $product_id = $ecommerce->product_soi($sku, $name, '', $description, '', '');
            //add-product-availability
            $availability_id = $ecommerce->availability_soi($product_id, $am_store_id);
            //find sku
            $sku_id = $ecommerce->skuSoi($sku);
            //add price
            $price_id = $ecommerce->priceSoi($sku_id, $price, $am_store_id);
            //normalize condition
            $condition = $ecommerce->normalCondition($product_condition);
            //find condition id
            $condition_id = $ecommerce->conditionSoi($condition);
            //add stock to sku
            $stock_id = $ecommerce->stockSoi($sku_id, $condition_id);
            $channel_array = array(
                'store_id' => $am_store_id,
                'stock_id' => $stock_id,
                'store_listing_id' => $store_listing_id,
                'url' => $url,
                'title' => $name,
                'description' => $description,
                'sku' => $sku,
                'price' => $price,
                'product_condition' => $product_condition,
                'inventory_level' => $inventory_level,
                'open_date' => $open_date,
                'photo_url' => $image_url,
                'item_is_marketplace' => $item_is_marketplace,
                'product_id_type' => $product_id_type,
                'zshop_shipping_fee' => $zshop_shipping_fee,
                'item_note' => $item_note,
                'zshop_category1' => $zshop_category1,
                'zshop_browse_path' => $zshop_browse_path,
                'zshop_storefront_feature' => $zshop_storefront_feature,
                'asin1' => $asin1,
                'asin2' => $asin2,
                'asin3' => $asin3,
                'will_ship_internationally' => $will_ship_internationally,
                'expedited_shipping' => $expedited_shipping,
                'zshop_boldface' => $zshop_boldface,
                'product_id' => $product_id,
                'bid_for_featured_placement' => $bid_for_featured_placement,
                'add_delete' => $add_delete,
                'pending_quantity' => $pending_quantity,
                'fulfillment_channel' => $fulfillment_channel
            );

            $listing_id = $ecommerce->listing_soi('listing_amazon', $am_store_id, $stock_id, $channel_array, 'true');
            echo $listing_id . '<br>';
            $x++;
        } else {
            return;
        }
    }
}
$end_time = microtime(true);
$execution_time = ($end_time - $start_time) / 60;
echo "Execution time: $execution_time mins";

?>
