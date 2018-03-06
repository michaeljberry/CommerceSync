<?php

namespace Amazon;

use Ecommerce\Ecommerce;
use controllers\channels\CurlController;
use controllers\channels\XMLController;

trait AmazonClientCurl
{

    protected static function sign($arr, $amazonAPI)
    {

        $sign = $amazonAPI::getMethod();
        $sign .= "\n";
        $sign .= Ecommerce::removeUrlProtocol($amazonAPI::getEndpoint());
        $sign .= "\n/";
        $sign .= $amazonAPI::getFeed();
        $sign .= "/";
        $sign .= $amazonAPI::getParameterByKey('Version');
        $sign .= "\n";
        $sign .= $arr;
        return $sign;

    }

    protected static function buildHeader($amazonXmlFeed = '')
    {

        $httpHeader = array();
        $httpHeader[] = 'Transfer-Encoding: chunked';
        $httpHeader[] = 'Content-Type: application/xml';
        $httpHeader[] = 'Content-MD5: ' . base64_encode(md5($amazonXmlFeed, true));
        $httpHeader[] = 'Expect:';
        $httpHeader[] = 'Accept:';
        return $httpHeader;

    }

    protected static function setCurlOptions($url, $headers = null, $post_string = null)
    {

        $request = curl_init($url);

        if ($headers)
        {

            curl_setopt($request, CURLOPT_HTTPHEADER, $headers);

        }

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($request, CURLOPT_POST, 1);

        if ($post_string)
        {

            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);

        }

        return $request;

    }

    protected static function createUrlArray($amazonAPI)
    {

        $parameters = $amazonAPI::getCurlParameters();
        $url = [];

        foreach ($parameters as $key => $val)
        {

            if($key === "FeedContent")
            {

                continue;

            }

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

    protected static function createLink($amazonAPI)
    {

        $url = static::createUrlArray($amazonAPI);
        usort($url, [get_called_class(), "cmp"]);

        $arr = implode('&', $url);
        $sign = static::sign($arr, $amazonAPI);

        $signature = static::encodeSignature($sign);

        $link = $amazonAPI::getEndpoint();
        $link .= "/";
        $link .= $amazonAPI::getFeed();
        $link .= "/";
        $link .= $amazonAPI::getParameterByKey('Version');
        $link .= "?$arr&Signature=$signature";
        return $link;

    }

    protected static function cmp($a, $b)
    {

        $a = substr($a, 0, strpos($a, "="));
        $b = substr($b, 0, strpos($b, "="));
        return ($a < $b) ? -1 : 1;

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
        $parameters = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"';
        $header = XMLController::openingXMLTag($request, $parameters);
        $header .= static::parseXML($xml);

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

        if (is_array($xml))
        {

            $amazonXml .= XMLController::makeXML($xml);

        } else {

            $amazonXml .= $xml;

        }

        return $amazonXml;

    }

    protected static function parseAmazonXML($amazonAPI)
    {

        $amazonXML = '';

        if ($amazonAPI::getParameterByKey("FeedContent"))
        {

            $amazonXML = XMLController::xmlOpenTag();
            $amazonXML .= static::xmlAmazonEnvelopeHeader();
            $amazonXML .= static::parseXML($amazonAPI::getParameterByKey("FeedContent"));
            $amazonXML .= static::xmlAmazonEnvelopeFooter();

        }

        return $amazonXML;

    }

    public static function amazonCurl($amazonAPI)
    {

        $amazonXmlFeed = static::parseAmazonXML($amazonAPI);
        Ecommerce::dd($amazonXmlFeed);
        $link = static::createLink($amazonAPI);
        $httpHeader = static::buildHeader($amazonXmlFeed);
        $request = static::setCurlOptions($link, $httpHeader, $amazonXmlFeed);
        // curl_setopt($request, CURLINFO_HEADER_OUT, true);
        return CurlController::request($request);

    }

}
