<?php

namespace rev;

use ecommerce\Ecommerce;
use models\channels\Curl;

trait ReverbClientCurl
{
    public function getAuthorizationToken($email, $password)
    {
        $url = 'https://reverb.com/api/auth/email';
        $post_string = '{"email":"' . $email . '","password":"' . $password . '"}';
        $response = ReverbClientCurl::reverbCurl($url, 'POST', $post_string);
        return $response;
    }

    protected static function createHeader()
    {
        $headers = [
            "Content-type: application/hal+json",
            "Authorization: Bearer " . ReverbClient::getAuthToken(),
            "Accept: application/hal+json",
            "Accept-Version: 3.0"
        ];
        return $headers;
    }

    public static function setCurlOptions($url, $method, $headers, $post_string = null)
    {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_HEADER, false);
        if ($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        return $request;
    }

    public static function reverbCurl($url, $method, $post_string = '')
    {
        $headers = ReverbClient::createHeader();
        $request = ReverbClient::setCurlOptions($url, $method, $headers, $post_string);
        $response = Curl::request($request);
        return $response;
    }
}