<?php

namespace eb;

use models\channels\ChannelModel;
use Crypt;
use ecommerce\EcommerceInterface;
use ecommerce\ecommerce as ecom;

class EbayClient implements EcommerceInterface
{
    protected $eBayDevID;
    protected $eBayAppID;
    protected $eBayCertID;
    protected $eBayToken;
    public $eBayStoreID;
    private $ebinfo;

    public function __construct($user_id){
        $this->setInfo($user_id);
        $this->setDevID();
        $this->setAppID();
        $this->setCertID();
        $this->setToken();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_ebay';
        $channel = 'Ebay';
        $columns = [
            'store_id',
            'devid',
            'appid',
            'certid',
            'token'
        ];

        $this->ebinfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setDevID()
    {
        $this->eBayDevID = Crypt::decrypt($this->ebinfo['devid']);
    }

    private function setAppID()
    {
        $this->eBayAppID = Crypt::decrypt($this->ebinfo['appid']);
    }

    private function setCertID()
    {
        $this->eBayCertID = Crypt::decrypt($this->ebinfo['certid']);
    }

    private function setToken()
    {
        $this->eBayToken = Crypt::decrypt($this->ebinfo['token']);
    }

    private function setStoreID()
    {
        $this->eBayStoreID = $this->ebinfo['store_id'];
    }

    public function getDevID()
    {
        return $this->eBayDevID;
    }

    public function getAppID()
    {
        return $this->eBayAppID;
    }

    public function getCertID()
    {
        return $this->eBayCertID;
    }

    public function getToken()
    {
        return $this->eBayToken;
    }

    public function getStoreID()
    {
        return $this->eBayStoreID;
    }

    protected function createTradingHeader($post_string, $callName)
    {
        $headers = [
            "Content-type: text/xml",
            "Content-length: " . strlen($post_string),
            "Connection: close",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 997",
            "X-EBAY-API-DEV-NAME: $this->eBayDevID",
            "X-EBAY-API-APP-NAME: $this->eBayAppID",
            "X-EBAY-API-CERT-NAME: $this->eBayCertID",
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
            "X-EBAY-SOA-SECURITY-APPNAME: $this->eBayAppID",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML"
        ];
        return $headers;
    }

    protected function createMerchandisingHeader($callName){
        $headers = [
            "X-EBAY-SOA-OPERATION-NAME: $callName",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-SERVICE-VERSION: 1.5.0",
            "EBAY-SOA-CONSUMER-ID: $this->eBayAppID",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-US"
        ];
        return $headers;
    }

    protected function createShoppingHeader($callName)
    {
        $headers = [
            "X-EBAY-API-APP-ID: $this->eBayAppID",
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-REQUEST-ENCODING: XML",
            "X-EBAY-API-VERSION: 997",
            "Content-type: text/xml",
        ];
        return $headers;
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

    protected static function headerParameter($callType)
    {
        $param = '';

        if($callType === 'trading' || $callType === 'shopping'){
            $param = 'xmlns="urn:ebay:apis:eBLBaseComponents"';
        }elseif ($callType === 'finding'){
            $param = 'xmlns="http://www.ebay.com/marketplace/search/v1/services"';
        }

        return $param;
    }

    protected function eBayCredentialsXML()
    {
        $credentialTag = 'RequesterCredentials';
        $credentials = ecom::openXMLParentTag($credentialTag);
        $credentials .= ecom::xmlTag('eBayAuthToken', $this->eBayToken);
        $credentials .= ecom::closeXMLParentTag($credentialTag);
        return $credentials;
    }

    protected function xmlHeader($requestName, $callType)
    {
        $header = ecom::xmlOpenTag();
        $request = $requestName . 'Request';
        $param = EbayClient::headerParameter($callType);
        $header .= ecom::openXMLParentTag($request, $param);
        if($callType !== 'finding' && $callType !== 'shopping') {
            $header .= $this->eBayCredentialsXML();
        }
        return $header;
    }

    protected static function xmlFooter($requestName)
    {
        $request = $requestName . 'Request';
        $footer = ecom::closeXMLParentTag($request);
        return $footer;
    }

    protected static function setCurlUrl($callType = 'trading')
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

    protected function curlPostString($requestName, $xml, $callType)
    {
        $post_string = $this->xmlHeader($requestName, $callType);
        $post_string .= ecom::makeXML($xml);
        $post_string .= EbayClient::xmlFooter($requestName);
        return $post_string;
    }

    protected static function setCurlOptions($headers, $post_string, $url)
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

    public function ebayCurl($requestName, $xml, $callType = 'trading')
    {
        $post_string = $this->curlPostString($requestName, $xml, $callType);
        $headers = $this->createHeader($post_string, $requestName, $callType);
        $curlUrl = EbayClient::setCurlUrl($callType);
        $request = EbayClient::setCurlOptions($headers, $post_string, $curlUrl);
        $response = ecom::curlRequest($request);

        return $response;
    }

}