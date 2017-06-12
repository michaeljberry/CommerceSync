<?php
set_time_limit(3600);
error_reporting(-1);
include __DIR__ . '/../../core/init.php';

$start_time = microtime(true);
$user_id = 838;
require WEBPLUGIN . 'am/amvar.php';

define('AWS_ACCESS_KEY_ID', $aminv->am_aws_access_key);
define('AWS_SECRET_ACCESS_KEY', $aminv->am_secret_key);
define('MERCHANT_ID', $aminv->am_merchant_id);
define('MARKETPLACE_ID', $aminv->am_marketplace_id);

$report = true;

echo 'Now: ' . date('m/d/y h:i:s') . '<br />';

include_once ('/var/www/html/portal/plugin/am/MarketplaceWebService/Samples/.config.inc.php');

include_once ('functions.php');

$serviceUrl = "https://mws.amazonservices.com";
$config = array (
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

echo '<br />';
$parameters = array (
    'Marketplace' => MARKETPLACE_ID,
    'Merchant' => MERCHANT_ID,
    'ReportType' => '_GET_FLAT_FILE_OPEN_LISTINGS_DATA_',
);
echo '<br /><br/>Request Report Request:<br><br>';
$request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
print_r($request);
invokeRequestReport($service, $request);
echo '<br /><br/>';