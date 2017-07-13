<?php

namespace eb;

use ecommerce\Ecommerce;
use controllers\channels\CurlController;
use controllers\channels\XMLController;

trait EbayClientCurl
{
    protected static function createTradingHeader($post_string, $callName)
    {
        $headers = [
            "Content-type: text/xml",
            "Content-length: " . strlen($post_string),
            "Connection: close",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 997",
            "X-EBAY-API-DEV-NAME: " . EbayClient::getDevID(),
            "X-EBAY-API-APP-NAME: " . EbayClient::getAppID(),
            "X-EBAY-API-CERT-NAME: " . EbayClient::getCertID(),
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DETAIL-LEVEL:0"
        ];
        return $headers;
    }

    protected static function createFindingHeader($callName)
    {
        $headers = [
            "X-EBAY-SOA-SERVICE-NAME: FindingService",
            "X-EBAY-SOA-OPERATION-NAME: $callName",
            "X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
            "X-EBAY-SOA-GLOBAL-ID: EBAY-US",
            "X-EBAY-SOA-SECURITY-APPNAME: " . EbayClient::getAppID(),
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML"
        ];
        return $headers;
    }

    protected static function createMerchandisingHeader($callName)
    {
        $headers = [
            "X-EBAY-SOA-OPERATION-NAME: $callName",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-SERVICE-VERSION: 1.5.0",
            "EBAY-SOA-CONSUMER-ID: " . EbayClient::getAppID(),
            "X-EBAY-SOA-GLOBAL-ID: EBAY-US"
        ];
        return $headers;
    }

    protected static function createShoppingHeader($callName)
    {
        $headers = [
            "X-EBAY-API-APP-ID: " . EbayClient::getAppID(),
            "X-EBAY-API-CALL-NAME: $callName",
            "X-EBAY-API-REQUEST-ENCODING: XML",
            "X-EBAY-API-VERSION: 997",
            "Content-type: text/xml",
        ];
        return $headers;
    }

    protected static function createHeader($post_string, $callName, $callType)
    {
        $headers = [];

        if ($callType === 'trading') {
            $headers = EbayClient::createTradingHeader($post_string, $callName);
        } elseif ($callType === 'finding') {
            $headers = EbayClient::createFindingHeader($callName);
        } elseif ($callType === 'merchandising') {
            $headers = EbayClient::createMerchandisingHeader($callName);
        } elseif ($callType === 'shopping') {
            $headers = EbayClient::createShoppingHeader($callName);
        }
        return $headers;
    }

    protected static function headerParameter($callType)
    {
        $param = '';

        if ($callType === 'trading' || $callType === 'shopping') {
            $param = 'xmlns="urn:ebay:apis:eBLBaseComponents"';
        } elseif ($callType === 'finding') {
            $param = 'xmlns="http://www.ebay.com/marketplace/search/v1/services"';
        }

        return $param;
    }

    protected static function eBayCredentialsXML()
    {
        $credentialTag = 'RequesterCredentials';
        $credentials = XMLController::openXMLParentTag($credentialTag);
        $credentials .= XMLController::xmlTag('eBayAuthToken', EbayClient::getToken());
        $credentials .= XMLController::closeXMLParentTag($credentialTag);
        return $credentials;
    }

    protected static function xmlHeader($requestName, $callType)
    {
        $header = XMLController::xmlOpenTag();
        $request = $requestName . 'Request';
        $param = EbayClient::headerParameter($callType);
        $header .= XMLController::openXMLParentTag($request, $param);
        if ($callType !== 'finding' && $callType !== 'shopping') {
            $header .= EbayClient::eBayCredentialsXML();
        }
        return $header;
    }

    protected static function xmlFooter($requestName)
    {
        $request = $requestName . 'Request';
        $footer = XMLController::closeXMLParentTag($request);
        return $footer;
    }

    protected static function setCurlUrl($callType = 'trading')
    {
        $url = '';

        if ($callType === 'trading') {
            $url = 'https://api.ebay.com/ws/api.dll';
        } elseif ($callType === 'finding') {
            $url = 'http://svcs.ebay.com/services/search/FindingService/v1';
        } elseif ($callType === 'merchandising') {
            $url = 'http://svcs.ebay.com/MerchandisingService?';
        } elseif ($callType === 'shopping') {
            $url = 'http://open.api.ebay.com/shopping?';
        }

        return $url;
    }

    protected static function curlPostString($requestName, $xml, $callType)
    {
        $post_string = EbayClient::xmlHeader($requestName, $callType);
        $post_string .= XMLController::makeXML($xml);
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

    public static function ebayCurl($requestName, $xml, $callType = 'trading')
    {
        $post_string = EbayClient::curlPostString($requestName, $xml, $callType);
        $headers = EbayClient::createHeader($post_string, $requestName, $callType);
        $curlUrl = EbayClient::setCurlUrl($callType);
        $request = EbayClient::setCurlOptions($headers, $post_string, $curlUrl);
        return CurlController::request($request);
    }
}