<?php

namespace Amazon;

use ecommerce\Ecommerce;
use controllers\channels\CurlController;
use controllers\channels\XMLController;

trait AmazonClientCurl
{

    private static $signatureMethod = "HmacSHA256";
    private static $signatureVersion = "2";
    private static $curlParameters = [];

    public static function setSignatureMethodParameter()
    {

        static::$curlParameters['SignatureMethod'] = static::$signatureMethod;

    }

    public static function setSignatureVersionParameter()
    {

        static::$curlParameters['SignatureVersion'] = static::$signatureVersion;

    }

    protected static function setTimestampParameter()
    {

        static::$curlParameters['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());

    }

    protected static function setAwsAccessKeyParameter()
    {

        static::$curlParameters['AWSAccessKeyId'] = AmazonClient::getAwsAccessKey();

    }

    protected static function setActionParameter($action)
    {

        static::$curlParameters['Action'] = $action;

    }

    protected static function setMerchantIdParameter($key)
    {

        static::$curlParameters[$key] = AmazonClient::getMerchantId();

    }

    protected static function setPurgeAndReplaceParameter()
    {

        static::$curlParameters['PurgeAndReplace'] = 'false';

    }

    protected static function setMarketplaceIdParameter($key)
    {

        static::$curlParameters[$key] = AmazonClient::getMarketplaceId();

    }

    protected static function setFeedTypeParameter($feedtype)
    {

        if (!empty($feedtype)) {

            $param['FeedType'] = $feedtype;

        }

    }

    protected static function setVersionDateParameter($feed)
    {

        static::$curlParameters['Version'] = AmazonClient::getAPIFeedInfo($feed)['versionDate'];

    }

    protected static function setParameterByKey($key, $value)
    {

        static::$curlParameters[$key] = $value;

    }

    protected static function getParameterByKey($key)
    {

        return static::$curlParameters[$key];

    }

    protected static function getCurlParameters()
    {

        return static::$curlParameters;

    }

    public static function setParams($action, $feedtype, $feed, $paramAdditionalConfig = [])
    {

        // $param = [];
        // $param['AWSAccessKeyId'] = AmazonClient::getAwsAccessKey();
        static::setAwsAccessKeyParameter();
        // $param['Action'] = $action;
        static::setActionParameter($action);

        //Parse $paramAdditionalConfig Array
        if (in_array('Merchant', $paramAdditionalConfig))
            // $param['Merchant'] = AmazonClient::getMerchantId();
            static::setMerchantIdParameter('Merchant');
        if (in_array('SellerId', $paramAdditionalConfig))
            // $param['SellerId'] = AmazonClient::getMerchantId();
            static::setMerchantIdParameter('SellerId');
        if (in_array('MarketplaceId.Id.1', $paramAdditionalConfig))
            // $param['MarketplaceId.Id.1'] = AmazonClient::getMarketplaceId();
            static::setMarketplaceIdParameter('MarketplaceId.Id.1');
        if (in_array('MarketplaceId', $paramAdditionalConfig))
            // $param['MarketplaceId'] = AmazonClient::getMarketplaceId();
            static::setMarketplaceIdParameter('MarketplaceId');
        if (in_array('PurgeAndReplace', $paramAdditionalConfig))
            // $param['PurgeAndReplace'] = 'false';
            static::setPurgeAndReplaceParameter();


        // if (!empty($feedtype)) {

        //     $param['FeedType'] = $feedtype;

        // }
        static::setFeedTypeParameter($feedtype);

        // $param['SignatureMethod'] = 'HmacSHA256';
        static::setSignatureMethodParameter();
        // $param['SignatureVersion'] = '2';
        static::setSignatureVersionParameter();
        // $param['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());
        static::setTimestampParameter();
        // $param['Version'] = $versionDate;
        static::setVersionDateParameter($feed);

        // return $param;

    }

    protected static function sign($arr, $whatToDo, $versionDate, $feed)
    {

        $sign = $whatToDo . "\n";
        $sign .= 'mws.amazonservices.com' . "\n";
        $sign .= '/' . $feed . '/' . $versionDate . "\n";
        $sign .= $arr;
        return $sign;

    }

    protected static function buildHeader($amazon_feed = '')
    {

        $httpHeader = array();
        $httpHeader[] = 'Transfer-Encoding: chunked';
        $httpHeader[] = 'Content-Type: application/xml';
        $httpHeader[] = 'Content-MD5: ' . base64_encode(md5($amazon_feed, true));
        $httpHeader[] = 'Expect:';
        $httpHeader[] = 'Accept:';
        return $httpHeader;

    }

    protected static function setCurlOptions($url, $headers = null, $post_string = null)
    {

        $request = curl_init($url);

        if ($headers) {

            curl_setopt($request, CURLOPT_HTTPHEADER, $headers);

        }

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($request, CURLOPT_POST, 1);

        if ($post_string) {

            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);

        }

        return $request;

    }

    protected static function createUrlArray()
    {

        $param = static::getCurlParameters();
        $url = [];
        foreach ($param as $key => $val) {

            $key = str_replace("%7E", "~", rawurlencode($key));
            $val = str_replace("%7E", "~", rawurlencode($val));
            $url[] = "{$key}={$val}";

        }

        return $url;

    }

    protected static function encodeSignature($sign)
    {

        $signature = hash_hmac("sha256", $sign, AmazonClient::getSecretKey(), true);
        $signature = urlencode(base64_encode($signature));
        return $signature;

    }

    protected static function createLink($feed, $whatToDo)
    {

        $url = AmazonClient::createUrlArray();
        usort($url, [get_called_class(), "cmp"]);

        $arr = implode('&', $url);
        $sign = AmazonClient::sign($arr, $whatToDo, static::getParameterByKey('Version'), $feed);

        $signature = AmazonClient::encodeSignature($sign);

        $link = "https://mws.amazonservices.com/$feed/";
        $link .= static::getParameterByKey('Version');
        $link .= "?$arr&Signature=$signature";
        return $link;

    }

    protected static function xmlAmazonEnvelopeHeader()
    {

        $xml = [
            'Header' => [
                'DocumentVersion' => '1.01',
                'MerchantIdentifier' => AmazonClient::getMerchantId()
            ]
        ];

        $request = 'AmazonEnvelope';
        $param = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"';
        $header = XMLController::openingXMLTag($request, $param);
        $header .= XMLController::makeXML($xml);

        return $header;

    }

    protected static function xmlAmazonEnvelopeFooter()
    {

        $request = 'AmazonEnvelope';
        $footer = XMLController::closingXMLTag($request);
        return $footer;

    }

    protected static function parseXML($xml)
    {

        $amazonXml = '';

        if (is_array($xml)) {

            $amazonXml .= XMLController::makeXML($xml);

        } else {

            $amazonXml .= $xml;

        }

        return $amazonXml;

    }

    protected static function parseAmazonXML($xml)
    {

        $amazonXML = '';

        if ($xml) {

            $amazonXML = XMLController::xmlOpenTag();
            $amazonXML .= AmazonClient::xmlAmazonEnvelopeHeader();
            $amazonXML .= AmazonClient::parseXML($xml);
            $amazonXML .= AmazonClient::xmlAmazonEnvelopeFooter();

        }

        return $amazonXML;

    }

    public static function amazonCurl($xml, $feed, $whatToDo)
    {

        $amazon_feed = AmazonClient::parseAmazonXML($xml);
        Ecommerce::dd($amazon_feed);
        $link = AmazonClient::createLink($feed, $whatToDo);
        $httpHeader = AmazonClient::buildHeader($amazon_feed);
        $request = AmazonClient::setCurlOptions($link, $httpHeader, $amazon_feed);
        return CurlController::request($request);

    }

    protected static function cmp($a, $b)
    {

        $a = substr($a, 0, strpos($a, "="));
        $b = substr($b, 0, strpos($b, "="));
        return ($a < $b) ? -1 : 1;

    }

}
