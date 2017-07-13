<?php

namespace wc;

use ecommerce\Ecommerce;
use controllers\channels\CurlController;

trait WooCommerceClientCurl
{
    protected static function createHeader($method, $post_string)
    {
        $headers = [
            'Content-Type: application/json'
        ];
        if ($method === 'POST' || $method === 'PUT') {
            $headers[] = 'Content-Length: ' . strlen($post_string);
        }
        return $headers;
    }

    protected static function setCurlOptions($url, $method, $post_string)
    {
        $request = curl_init($url);
        $headers = WooCommerceClientCurl::createHeader($method, $post_string);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_USERPWD, WooCommerceClient::getConsumerKey() . ":" . WooCommerceClient::getSecretKey());
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        if ($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        return $request;
    }

    public static function woocommerceCurl($url, $method, $post_string = null)
    {
        $request = WooCommerceClientCurl::setCurlOptions($url, $method, $post_string);
        return CurlController::request($request);
    }
}