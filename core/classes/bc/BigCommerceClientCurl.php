<?php

namespace bc;

use ecommerce\Ecommerce;

trait BigCommerceClientCurl
{
    protected static function setCurlOptions($url, $method, $post_string)
    {
        $request = curl_init($url);
        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($request, CURLOPT_HTTPHEADER,
                ['Content-type: application/json', 'Content-Length: ' . strlen($post_string)]
            );
        } elseif ($method === 'GET') {
            curl_setopt($request, CURLOPT_HTTPHEADER,
                ['Accept: application/json', 'Content-Length: 0']
            );
        }

        if ($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_USERPWD, BigCommerceClient::getUsername() . ":" . BigCommerceClient::getAPIKey());
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        return $request;
    }

    public static function bigcommerceCurl($url, $method, $post_string = null)
    {
        $request = BigCommerceClient::setCurlOptions($url, $method, $post_string);
        return Ecommerce::curlRequest($request);
    }
}