<?php

namespace eb;

use ecommerceclass\ecommerceclass as ecom;
use models\channels\channelModel;

class ebayclass
{
    public $db;
    public $eb_dev_id;
    public $eb_app_id;
    public $eb_cert_id;
    public $eb_token;
    public $eb_store_id;

    public function __construct($ebayclient){
        $this->db = channelModel::getDBInstance();
        $this->eb_dev_id = $ebayclient->getDevID();
        $this->eb_app_id = $ebayclient->getAppID();
        $this->eb_cert_id = $ebayclient->getCertID();
        $this->eb_token = $ebayclient->getToken();
        $this->eb_store_id = $ebayclient->getStoreID();
    }
    public function sanitize_column_name($col){
        switch($col){
            case $col == "token":
                $column = 'token';
                break;
            case $col == "devid":
                $column = 'devid';
                break;
            case $col == "sandbox_devid":
                $column = 'sandbox_devid';
                break;
            case $col == "sandbox_token":
                $column = 'sandbox_token';
                break;
            case $col == "sandbox_appid":
                $column = 'sandbox_appid';
                break;
            case $col == "sandbox_certid":
                $column = 'sandbox_certid';
                break;
        }
        return $column;
    }

    public function update_app_info($crypt, $store_id, $column, $id){
        $column = $this->sanitize_column_name($column);
        $query = $this->db->prepare("UPDATE api_ebay SET $column = :id WHERE store_id = :store_id");
        $query_params = [
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        ];
        $query->execute($query_params);
        return true;
    }
    public function get_ebay_app_id($user_id, $sand = null){
        if(empty($sand)) {
            $query = $this->db->prepare("SELECT store_id, devid, appid, certid, token FROM api_ebay INNER JOIN store ON api_ebay.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Ebay'");
            $query_params = [
                ':user_id' => $user_id
            ];
            $query->execute($query_params);
        }else{
            $query = $this->db->prepare("SELECT store_id, sandbox_devid AS devid, sandbox_appid AS appid, sandbox_certid AS certid, sandbox_token AS token FROM api_ebay INNER JOIN store ON api_ebay.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Ebay'");
            $query_params = [
                ':user_id' => $user_id
            ];
            $query->execute($query_params);
        }
        return $query->fetch();
    }
    public function get_listings($item_id = null){
        if(!$item_id) {
            $query = $this->db->prepare("SELECT id, store_listing_id, price FROM listing_ebay");
            $query->execute();
            return $query->fetchAll();
        }else{
            $query = $this->db->prepare("SELECT id FROM listing_ebay WHERE store_listing_id = :item_id");
            $query_params = [
                'item_id' => $item_id
            ];
            $query->execute($query_params);
            return $query->fetchColumn();
        }
    }

    public function get_recently_updated_listings()
    {
        $query = $this->db->prepare("SELECT store_listing_id, description FROM listing_ebay WHERE DATE(last_edited) = CURRENT_DATE ");
        $query->execute();
        return $query->fetchAll();
    }

    public function get_listing_upc(){
        $query = $this->db->prepare("SELECT le.id, le.store_listing_id, le.sku, p.upc FROM listing_ebay le LEFT JOIN sku sk ON le.sku = sk.sku LEFT JOIN product p ON p.id = sk.product_id");
        $query->execute();
        return $query->fetchAll();
    }

    public function get_listing_id($sku){
        $query = $this->db->prepare("SELECT store_listing_id FROM listing_ebay WHERE sku = :sku");
        $query_params = [
            ':sku' => $sku
        ];
        $query->execute($query_params);
        return $query->fetchColumn();
    }

    public function get_transaction_id($eb_dev_id, $eb_app_id, $eb_cert_id, $eb_token, $item_id){
        $requestName = 'GetItemTransactions';

        $xml = [
            'ItemID' => $item_id
        ];

        $response = $this->ebayCurl($requestName, $xml);
        return $response;
    }

    public function ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged, $propose = null, $increaseBy = 0){

        $ebayFeeMax = 250;

        $paypalFeePercent = .029; //Round up
        $paypalFeeFlat = 0.30;

        $cost = ecom::formatMoney($cost);

        $costOfQty = $cost * $quantity;

        if(empty($propose)) {
            $totalPrice = $pl10 * $quantity;
        }elseif(empty($increaseBy)){
            $totalPrice = ecom::roundMoney($costOfQty/(1-($minimumProfitPercent/100)));
            $pl10 = ecom::formatMoney($totalPrice/$quantity);
        }else{
            $totalPrice = ecom::formatMoney($increaseBy);
            $pl10 = ecom::formatMoney($totalPrice/$quantity);
        }

        $shippingCost = 3.99; //Amount we paid to ship the product

        $ebayFeePercent = $ecommerce->getCategoryFeeOfSKU('categories_ebay', 'listing_ebay', $sku);

        $shippingCollected = ecom::formatMoney($shippingIncludedInPrice ? $shippingCharged : 0);

        $ebayTotalFee = ecom::roundMoney((($totalPrice + $shippingCollected) * $ebayFeePercent) < $ebayFeeMax ? (($totalPrice + $shippingCollected) * $ebayFeePercent) : $ebayFeeMax);

        $paypalTotalFee = ecom::roundMoney(($totalPrice + $shippingCollected) * $paypalFeePercent) + $paypalFeeFlat;

        $totalFees = ecom::formatMoney($ebayTotalFee + $paypalTotalFee + $shippingCost);

        $totalCost = $costOfQty + $totalFees;

        $grossProfit = $totalPrice + $shippingCollected - $costOfQty;
        $grossProfitPercent = ecom::formatMoney($grossProfit / $totalPrice, 4) * 100;

        $netProfit = ecom::formatMoney($grossProfit - $ebayTotalFee - $paypalTotalFee - $shippingCost);
        $netProfitPercent = ecom::formatMoney($netProfit / $totalPrice, 4) * 100;

        if(!empty($propose) && ($grossProfitPercent < $minimumProfitPercent || $netProfitPercent < $minimumNetProfitPercent)){
            $totalPrice = $totalPrice + $increment;
            $totalPrice = ecom::formatMoney($totalPrice);
            $priceArray = $this->ebay_pricing($ecommerce, $minimumProfitPercent, $minimumNetProfitPercent, $increment, $sku, $quantity, $msrp, $pl10, $pl1, $cost, $shippingIncludedInPrice, $shippingCharged, 1, $totalPrice);
        }else {
            $priceArray = compact(
                'sku','quantity','msrp','pl10','pl1','cost','totalPrice',
                'totalCost','shippingCollected','shippingCost','ebayFeePercent',
                'ebayFeeMax','ebayTotalFee','paypalFeePercent','paypalFeeFlat','paypalTotalFee',
                'minimumProfitPercent','totalFees','grossProfit','grossProfitPercent','netProfit',
                'netProfitPercent'
            );
        }
        return $priceArray;
    }

    public function pricingTables($priceArray)
    {
        $tableArray = [
            [
                'Qty' => $priceArray['quantity'],
                'pl10' => $priceArray['pl10'],
                'totalPrice' => [
                    'value' => $priceArray['totalPrice'],
                    'format' => 'revenue'
                ],
                'shippingCollected' => [
                    'value' => $priceArray['shippingCollected'],
                    'format' => 'revenue'
                ],
                'shippingCost' => [
                    'value' => $priceArray['shippingCost'],
                    'format' => 'expense'
                ],
                'ebayTotalFee' => [
                    'value' => $priceArray['ebayTotalFee'],
                    'format' => 'expense'
                ],
                'paypalTotalFee' => [
                    'value' => $priceArray['paypalTotalFee'],
                    'format' => 'expense'
                ],
                'cost' => [
                    'value' => $priceArray['cost'],
                    'format' => 'expense'
                ],
                'totalCost ((Qty x Cost) + Fees)' => [
                    'value' => $priceArray['totalCost'],
                    'format' => 'expense'
                ],
                'grossProfit' => [
                    'value' => $priceArray['grossProfit'],
                    'display' => "{$priceArray['grossProfit']} ({$priceArray['grossProfitPercent']}%)",
                    'format' => 'aboveZero'
                ],
                'netProfit' => [
                    'value' => $priceArray['netProfit'],
                    'display' => "{$priceArray['netProfit']} ({$priceArray['netProfitPercent']}%)",
                    'format' => 'aboveZero'
                ],
            ]
        ];
        return $tableArray;
    }

    public function get_order_days($store_id){
        $query = $this->db->prepare("SELECT api_days FROM api_ebay WHERE store_id = :store_id");
        $query_params = [
            ':store_id' => $store_id
        ];
        $query->execute($query_params);
        return $query->fetchColumn();
    }
    public function set_order_days($store_id, $days){
        $query = $this->db->prepare("UPDATE api_ebay SET api_days = :api_days WHERE store_id = :store_id");
        $query_params = [
            ':store_id' => $store_id,
            ':api_days' => $days
        ];
        $query->execute($query_params);
        return true;
    }

    protected function createHeader($post_string, $callName, $callType)
    {
        $headers = [];

        if($callType === 'trading') {
            $headers = $this->createTradingHeader($post_string, $callName);
        }elseif ($callType === 'finding'){
            $headers = $this->createFindingHeader($callName);
        }elseif($callType === 'merchandising'){
            $headers = $this->createMerchandisingHeader($callName);
        }elseif($callType === 'shopping'){
            $headers = $this->createShoppingHeader($callName);
        }
        return $headers;
    }

    protected function createTradingHeader($post_string, $callName)
    {
        $headers = [
            "Content-type: text/xml",
            "Content-length: " . strlen($post_string),
            "Connection: close",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 997",
            "X-EBAY-API-DEV-NAME: $this->eb_dev_id",
            "X-EBAY-API-APP-NAME: $this->eb_app_id",
            "X-EBAY-API-CERT-NAME: $this->eb_cert_id",
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DETAIL-LEVEL:0"
        ];
        return $headers;
    }

        protected function createFindingHeader($callName){
        $headers = [
            "X-EBAY-SOA-SERVICE-NAME: FindingService",
            "X-EBAY-SOA-OPERATION-NAME: $callName",
            "X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-US",
            "X-EBAY-SOA-SECURITY-APPNAME: $this->eb_app_id",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML"
        ];
        return $headers;
    }

    protected function createMerchandisingHeader($callName){
        $headers = [
            "X-EBAY-SOA-OPERATION-NAME: $callName",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-SERVICE-VERSION: 1.5.0",
            "EBAY-SOA-CONSUMER-ID: $this->eb_app_id",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-US"
        ];
        return $headers;
    }

    protected function createShoppingHeader($callName)
    {
        $headers = [
            "X-EBAY-API-APP-ID: $this->eb_app_id",
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-REQUEST-ENCODING: XML",
            "X-EBAY-API-VERSION: 997",
            "Content-type: text/xml",
        ];
        return $headers;
    }

    protected function setCurlUrl($callType = 'trading')
    {
        $url = '';

        if($callType === 'trading') {
            $url = 'https://api.ebay.com/ws/api.dll';
        }elseif ($callType === 'finding'){
            $url = 'http://svcs.ebay.com/services/search/FindingService/v1';
        }elseif($callType === 'merchandising'){
            $url = 'http://svcs.ebay.com/MerchandisingService?';
        }elseif($callType === 'shopping'){
            $url = 'http://open.api.ebay.com/shopping?';
        }

        return $url;
    }

    protected function setCurlOptions($headers, $post_string, $url)
    {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_HEADER, false);
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        return $request;
    }

    protected function headerParameter($callType)
    {
        $param = '';

        if($callType === 'trading' || $callType === 'shopping'){
            $param = 'xmlns="urn:ebay:apis:eBLBaseComponents"';
        }elseif ($callType === 'finding'){
            $param = 'xmlns="http://www.ebay.com/marketplace/search/v1/services"';
        }

        return $param;
    }

    protected function xmlHeader($requestName, $callType)
    {
        $header = ecom::xmlOpenTag();
        $request = $requestName . 'Request';
        $param = $this->headerParameter($callType);
        $header .= ecom::openXMLParentTag($request, $param);
        if($callType !== 'finding' && $callType !== 'shopping') {
            $header .= $this->eBayCredentialsXML();
        }
        return $header;
    }

    protected function xmlFooter($requestName)
    {
        $request = $requestName . 'Request';
        $footer = ecom::closeXMLParentTag($request);
        return $footer;
    }

    protected function eBayCredentialsXML()
    {
        $credentialTag = 'RequesterCredentials';
        $credentials = ecom::openXMLParentTag($credentialTag);
        $credentials .= ecom::xmlTag('eBayAuthToken', $this->eb_token);
        $credentials .= ecom::closeXMLParentTag($credentialTag);
        return $credentials;
    }

    protected function curlPostString($requestName, $xml, $callType)
    {
        $post_string = $this->xmlHeader($requestName, $callType);
        $post_string .= ecom::makeXML($xml);
        $post_string .= $this->xmlFooter($requestName);
        return $post_string;
    }

    public function ebayCurl($requestName, $xml, $callType = 'trading')
    {
        $post_string = $this->curlPostString($requestName, $xml, $callType);
        $headers = $this->createHeader($post_string, $requestName, $callType);
        $curlUrl = $this->setCurlUrl($callType);
        $request = $this->setCurlOptions($headers, $post_string, $curlUrl);
        $response = ecom::curlRequest($request);

        return $response;
    }

}