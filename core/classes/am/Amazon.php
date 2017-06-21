<?php

namespace am;

use ecommerce\Ecommerce as ecom;
use models\channels\channelModel;

class Amazon
{
    public $db;
    protected $am_merchant_id;
    protected $am_marketplace_id;
    protected $am_aws_access_key;
    protected $am_secret_key;
    public $am_store_id;
    protected $apiFeedInfo = [
        'FulfillmentInventory' => [
            'versionDate' => '2010-10-01'
        ],
        'Feeds' => [
            'versionDate' => '2009-01-01'
        ],
        'Products' => [
            'versionDate' => '2011-10-01'
        ],
        'doc' => [
            'versionDate' => '2009-01-01'
        ]
    ];

    public function __construct($amclient){
        $this->db = channelModel::getDBInstance();
        $this->am_merchant_id = $amclient->getMerchantID();
        $this->am_marketplace_id = $amclient->getMarketplaceID();
        $this->am_aws_access_key = $amclient->getAWSAccessKey();
        $this->am_secret_key = $amclient->getSecretKey();
        $this->am_store_id = $amclient->getStoreID();
    }
    public function sanitizeColumnName($col){
        switch($col){
            case $col == "merchantid":
                $column = 'merchantid';
                break;
            case $col == "marketplaceid":
                $column = 'marketplaceid';
                break;
            case $col == "aws_access_key":
                $column = 'aws_access_key';
                break;
            case $col == "secret_key":
                $column = 'secret_key';
                break;
        }
        return $column;
    }
    //temp solution
    public function find_amazon_listing($store_listing_id){
        $query = $this->db->prepare("SELECT id FROM listing_amazon WHERE store_listing_id = :store_listing_id");
        $query_params = array(
            'store_listing_id' => $store_listing_id
        );
        $query->execute($query_params);
        return $query->fetchColumn();
    }
    public function get_amazon_app_id($user_id){
        $query = $this->db->prepare("SELECT store_id, merchantid, marketplaceid, aws_access_key, secret_key FROM api_amazon INNER JOIN store ON api_amazon.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Amazon'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
    public function save_app_info($crypt, $store_id, $merchant_id, $marketplace_id, $aws_access_key, $secret_key){
        $query = $this->db->prepare("INSERT INTO api_amazon (store_id, merchantid, marketplaceid, aws_access_key, secret_key) VALUES (:store_id, :merchantid, :marketplaceid, :aws_access_key, :secret_key)");
        $query_params = array(
            ":store_id" => $store_id,
            ":merchantid" => $crypt->encrypt($merchant_id),
            ":marketplaceid" => $crypt->encrypt($marketplace_id),
            ":aws_access_key" => $crypt->encrypt($aws_access_key)
        );
        $query->execute($query_params);
        return true;
    }
    public function update_app_info($crypt, $store_id, $column, $id){
        $column = $this->sanitizeColumnName($column);
        $query = $this->db->prepare("UPDATE api_amazon SET $column = :id WHERE store_id = :store_id");
        $query_params = array(
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        );
        $query->execute($query_params);
        return true;
    }
    public function get_order_dates($store_id){
        $query = $this->db->prepare("SELECT api_pullfrom, api_pullto FROM api_amazon WHERE store_id = :store_id");
        $query_params = [
            ':store_id' => $store_id
        ];
        $query->execute($query_params);
        return $query->fetch();
    }
    public function set_order_dates($store_id, $from, $to){
        $query = $this->db->prepare("UPDATE api_amazon SET api_pullfrom = :api_pullfrom, api_pullto = :api_pullto WHERE store_id = :store_id");
        $query_params = [
            ':store_id' => $store_id,
            ':api_pullfrom' => $from,
            ':api_pullto' => $to
        ];
        $query->execute($query_params);
        return true;
    }
    protected function setParams($action, $feedtype, $version, $paramAdditionalConfig = []){
        $param = [];
        $param['AWSAccessKeyId'] = $this->am_aws_access_key;
        $param['Action'] = $action;

        //Parse $paramAdditionalConfig Array
        if(in_array('Merchant', $paramAdditionalConfig))
            $param['Merchant'] = $this->am_merchant_id;
        if(in_array('MarketplaceId.Id.1', $paramAdditionalConfig))
            $param['MarketplaceId.Id.1'] = $this->am_marketplace_id;
        if(in_array('PurgeAndReplace',$paramAdditionalConfig))
            $param['PurgeAndReplace'] = 'false';
        if(in_array('MarketplaceId',$paramAdditionalConfig))
            $param['MarketplaceId'] = $this->am_marketplace_id;
        if(in_array('SellerId', $paramAdditionalConfig))
            $param['SellerId'] = $this->am_merchant_id;

        if(!empty($feedtype)) {
            $param['FeedType'] = $feedtype;
        }
        $param['SignatureMethod']  = 'HmacSHA256';
        $param['SignatureVersion'] = '2';
        $param['Timestamp']        = gmdate("Y-m-d\TH:i:s\Z", time());
        $param['Version']          = $version;

        return $param;
    }
    protected function sign($arr, $whatToDo, $version, $feed){
        $sign  = $whatToDo . "\n";
        $sign .= 'mws.amazonservices.com' . "\n";
        $sign .= '/' . $feed . '/' . $version . "\n";
        $sign .= $arr;
        return $sign;
    }
    protected function buildHeader($amazon_feed = ''){
        $httpHeader     =   array();
        $httpHeader[]   =   'Transfer-Encoding: chunked';
        $httpHeader[]   =   'Content-Type: application/xml';
        $httpHeader[]   =   'Content-MD5: ' . base64_encode(md5($amazon_feed, true));
        $httpHeader[]   =   'Expect:';
        $httpHeader[]   =   'Accept:';
        return $httpHeader;
    }

    protected function setCurlOptions($url, $headers = null, $post_string = null)
    {
        $request = curl_init($url);
        if($headers) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($request, CURLOPT_POST, 1);
        if($post_string){
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        return $request;
    }

    protected function createUrlArray($param)
    {
        $url = [];
        foreach ($param as $key => $val) {

            $key = str_replace("%7E", "~", rawurlencode($key));
            $val = str_replace("%7E", "~", rawurlencode($val));
            $url[] = "{$key}={$val}";
        }
        return $url;
    }

    protected function encodeSignature($sign)
    {
        $signature = hash_hmac("sha256", $sign, $this->am_secret_key, true);
        $signature = urlencode(base64_encode($signature));
        return $signature;
    }

    protected function createLink($feed, $version, $param, $whatToDo)
    {
        $url = $this->createUrlArray($param);
        usort($url, array($this,"cmp"));

        $arr   = implode('&', $url);
        $sign = $this->sign($arr, $whatToDo, $param['Version'], $feed);

        $signature = $this->encodeSignature($sign);

        $link  = "https://mws.amazonservices.com/$feed/$version?$arr&Signature=$signature";
        return $link;
    }

    protected function xmlAmazonEnvelopeHeader()
    {
        $xml = [
            'Header' => [
                'DocumentVersion' => '1.01',
                'MerchantIdentifier' => $this->am_merchant_id
            ]
        ];

        $request = 'AmazonEnvelope';
        $param = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"';
        $header = ecom::openXMLParentTag($request, $param);
        $header .= ecom::makeXML($xml);

        return $header;
    }

    protected function xmlAmazonEnvelopeFooter()
    {
        $request = 'AmazonEnvelope';
        $footer = ecom::closeXMLParentTag($request);
        return $footer;
    }

    protected function parseXML($xml)
    {
        $amazonXml = '';
        if(is_array($xml)){
            $amazonXml .= ecom::makeXML($xml);
        }else{
            $amazonXml .= $xml;
        }
        return $amazonXml;
    }

    protected function parseAmazonXML($xml)
    {
        $amazonXML = '';
        if($xml) {
            $amazonXML = ecom::xmlOpenTag();
            $amazonXML .= $this->xmlAmazonEnvelopeHeader();
            $amazonXML .= $this->parseXML($xml);
            $amazonXML .= $this->xmlAmazonEnvelopeFooter();
//            ecom::dd($amazonXML);
        }
        return $amazonXML;
    }

    public function amazonCurl($xml, $feed, $version, $param, $whatToDo)
    {
        $amazon_feed = $this->parseAmazonXML($xml);
        $link = $this->createLink($feed, $version, $param, $whatToDo);
        $httpHeader = $this->buildHeader($amazon_feed);
        $request = $this->setCurlOptions($link, $httpHeader, $amazon_feed);
        $response = ecom::curlRequest($request);
        return $response;
    }

    private static function cmp($a, $b){
        $a = substr($a, 0, strpos($a, "="));
        $b = substr($b, 0, strpos($b, "="));
        return ($a < $b) ? -1 : 1;
    }

}