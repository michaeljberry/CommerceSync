<?php

namespace am;

use controllers\channels\ChannelController;
use Crypt;
use ecommerce\EcommerceInterface;
use ecommerce\ecommerce as ecom;

class AmazonClient implements EcommerceInterface
{
    private $am_merchant_id;
    private $am_marketplace_id;
    private $am_aws_access_key;
    private $am_secret_key;
    public $am_store_id;
    private $aminfo;
    public $apiFeedInfo = [
        'FulfillmentInventory' => [
            'versionDate' => '2010-10-01'
        ],
        'Feeds' => [
            'versionDate' => '2009-01-01'
        ],
        'Products' => [
            'versionDate' => '2011-10-01'
        ],
        'Orders' => [
            'versionDate' => '2013-09-01'
        ],
        'doc' => [
            'versionDate' => '2009-01-01'
        ]
    ];

    public function __construct($user_id){
        $this->setInfo($user_id);
        $this->setMerchantID();
        $this->setMarketplaceID();
        $this->setAWSAccessKey();
        $this->setSecretKey();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_amazon';
        $channel = 'Amazon';
        $columns = [
            'store_id',
            'merchantid',
            'marketplaceid',
            'aws_access_key',
            'secret_key'
        ];

        $this->aminfo = ChannelController::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setMerchantID()
    {
        $this->am_merchant_id = Crypt::decrypt($this->aminfo['merchantid']);
    }

    private function setMarketplaceID()
    {
        $this->am_marketplace_id = Crypt::decrypt($this->aminfo['marketplaceid']);
    }

    private function setAWSAccessKey()
    {
        $this->am_aws_access_key = Crypt::decrypt($this->aminfo['aws_access_key']);
    }

    private function setSecretKey()
    {
        $this->am_secret_key = Crypt::decrypt($this->aminfo['secret_key']);
    }

    private function setStoreID()
    {
        $this->am_store_id = $this->aminfo['store_id'];
    }

    public function getMerchantID()
    {
        return $this->am_merchant_id;
    }
    public function getMarketplaceID()
    {
        return $this->am_marketplace_id;
    }
    public function getAWSAccessKey()
    {
        return $this->am_aws_access_key;
    }
    public function getSecretKey()
    {
        return $this->am_secret_key;
    }
    public function getStoreID()
    {
        return $this->am_store_id;
    }

    public function setParams($action, $feedtype, $version, $paramAdditionalConfig = []){
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

    protected static function sign($arr, $whatToDo, $version, $feed){
        $sign  = $whatToDo . "\n";
        $sign .= 'mws.amazonservices.com' . "\n";
        $sign .= '/' . $feed . '/' . $version . "\n";
        $sign .= $arr;
        return $sign;
    }

    protected static function buildHeader($amazon_feed = ''){
        $httpHeader     =   array();
        $httpHeader[]   =   'Transfer-Encoding: chunked';
        $httpHeader[]   =   'Content-Type: application/xml';
        $httpHeader[]   =   'Content-MD5: ' . base64_encode(md5($amazon_feed, true));
        $httpHeader[]   =   'Expect:';
        $httpHeader[]   =   'Accept:';
        return $httpHeader;
    }

    protected static function setCurlOptions($url, $headers = null, $post_string = null)
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

    protected static function createUrlArray($param)
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
        $url = AmazonClient::createUrlArray($param);
        usort($url, array($this,"cmp"));

        $arr   = implode('&', $url);
        $sign = AmazonClient::sign($arr, $whatToDo, $param['Version'], $feed);

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

    protected static function xmlAmazonEnvelopeFooter()
    {
        $request = 'AmazonEnvelope';
        $footer = ecom::closeXMLParentTag($request);
        return $footer;
    }

    protected static function parseXML($xml)
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
            $amazonXML .= AmazonClient::parseXML($xml);
            $amazonXML .= AmazonClient::xmlAmazonEnvelopeFooter();
        }
        return $amazonXML;
    }

    public function amazonCurl($xml, $feed, $version, $param, $whatToDo)
    {
        $amazon_feed = $this->parseAmazonXML($xml);
        $link = $this->createLink($feed, $version, $param, $whatToDo);
        $httpHeader = AmazonClient::buildHeader($amazon_feed);
        $request = AmazonClient::setCurlOptions($link, $httpHeader, $amazon_feed);
        $response = ecom::curlRequest($request);
        return $response;
    }

    private static function cmp($a, $b){
        $a = substr($a, 0, strpos($a, "="));
        $b = substr($b, 0, strpos($b, "="));
        return ($a < $b) ? -1 : 1;
    }

}