<?php

namespace am;

use ecommerce\Ecommerce;
use models\channels\Curl;
use models\channels\XML;

trait AmazonClientCurl
{
    /**
     * @param $action
     * @param $feedtype
     * @param $version
     * @param array $paramAdditionalConfig
     * @return array
     */
    public static function setParams($action, $feedtype, $version, $paramAdditionalConfig = [])
    {
        $param = [];
        $param['AWSAccessKeyId'] = AmazonClient::getAWSAccessKey();
        $param['Action'] = $action;

        //Parse $paramAdditionalConfig Array
        if (in_array('Merchant', $paramAdditionalConfig))
            $param['Merchant'] = AmazonClient::getMerchantID();
        if (in_array('MarketplaceId.Id.1', $paramAdditionalConfig))
            $param['MarketplaceId.Id.1'] = AmazonClient::getMarketplaceID();
        if (in_array('PurgeAndReplace', $paramAdditionalConfig))
            $param['PurgeAndReplace'] = 'false';
        if (in_array('MarketplaceId', $paramAdditionalConfig))
            $param['MarketplaceId'] = AmazonClient::getMarketplaceID();
        if (in_array('SellerId', $paramAdditionalConfig))
            $param['SellerId'] = AmazonClient::getMerchantID();

        if (!empty($feedtype)) {
            $param['FeedType'] = $feedtype;
        }
        $param['SignatureMethod'] = 'HmacSHA256';
        $param['SignatureVersion'] = '2';
        $param['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z", time());
        $param['Version'] = $version;

        return $param;
    }

    /**
     * @param $arr
     * @param $whatToDo
     * @param $version
     * @param $feed
     * @return string
     */
    protected static function sign($arr, $whatToDo, $version, $feed)
    {
        $sign = $whatToDo . "\n";
        $sign .= 'mws.amazonservices.com' . "\n";
        $sign .= '/' . $feed . '/' . $version . "\n";
        $sign .= $arr;
        return $sign;
    }

    /**
     * @param string $amazon_feed
     * @return array
     */
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

    /**
     * @param $url
     * @param null $headers
     * @param null $post_string
     * @return resource
     */
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

    /**
     * @param $param
     * @return array
     */
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

    /**
     * @param $sign
     * @return string
     */
    protected static function encodeSignature($sign)
    {
        $signature = hash_hmac("sha256", $sign, AmazonClient::getSecretKey(), true);
        $signature = urlencode(base64_encode($signature));
        return $signature;
    }

    /**
     * @param $feed
     * @param $version
     * @param $param
     * @param $whatToDo
     * @return string
     */
    protected static function createLink($feed, $version, $param, $whatToDo)
    {
        $url = AmazonClient::createUrlArray($param);
        usort($url, [get_called_class(), "cmp"]);

        $arr = implode('&', $url);
        $sign = AmazonClient::sign($arr, $whatToDo, $param['Version'], $feed);

        $signature = AmazonClient::encodeSignature($sign);

        $link = "https://mws.amazonservices.com/$feed/$version?$arr&Signature=$signature";
        return $link;
    }

    /**
     * @return string
     */
    protected static function xmlAmazonEnvelopeHeader()
    {
        $xml = [
            'Header' => [
                'DocumentVersion' => '1.01',
                'MerchantIdentifier' => AmazonClient::getMerchantID()
            ]
        ];

        $request = 'AmazonEnvelope';
        $param = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"';
        $header = XML::openXMLParentTag($request, $param);
        $header .= XML::makeXML($xml);

        return $header;
    }

    /**
     * @return string
     */
    protected static function xmlAmazonEnvelopeFooter()
    {
        $request = 'AmazonEnvelope';
        $footer = XML::closeXMLParentTag($request);
        return $footer;
    }

    /**
     * @param $xml
     * @return string
     */
    protected static function parseXML($xml)
    {
        $amazonXml = '';
        if (is_array($xml)) {
            $amazonXml .= XML::makeXML($xml);
        } else {
            $amazonXml .= $xml;
        }
        return $amazonXml;
    }

    /**
     * @param $xml
     * @return string
     */
    protected static function parseAmazonXML($xml)
    {
        $amazonXML = '';
        if ($xml) {
            $amazonXML = XML::xmlOpenTag();
            $amazonXML .= AmazonClient::xmlAmazonEnvelopeHeader();
            $amazonXML .= AmazonClient::parseXML($xml);
            $amazonXML .= AmazonClient::xmlAmazonEnvelopeFooter();
        }
        return $amazonXML;
    }

    /**
     * @param $xml
     * @param $feed
     * @param $version
     * @param $param
     * @param $whatToDo
     * @return mixed|string
     */
    public static function amazonCurl($xml, $feed, $version, $param, $whatToDo)
    {
        $amazon_feed = AmazonClient::parseAmazonXML($xml);
        $link = AmazonClient::createLink($feed, $version, $param, $whatToDo);
        $httpHeader = AmazonClient::buildHeader($amazon_feed);
        $request = AmazonClient::setCurlOptions($link, $httpHeader, $amazon_feed);
        $response = Curl::request($request);
        return $response;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    protected static function cmp($a, $b)
    {
        $a = substr($a, 0, strpos($a, "="));
        $b = substr($b, 0, strpos($b, "="));
        return ($a < $b) ? -1 : 1;
    }
}