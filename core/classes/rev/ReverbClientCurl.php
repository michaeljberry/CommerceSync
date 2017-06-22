<?php

namespace rev;

use ecommerce\Ecommerce;

trait ReverbClientCurl
{
    public function get_auth($email, $password){
        $url = 'https://reverb.com/api/auth/email';
        $post_string = '{"email":"' . $email . '","password":"' . $password . '"}';
        $response = $this->reverbCurl($url, 'POST', $post_string);
        return $response;
    }

    protected function createHeader()
    {
        $headers = [
            "Content-type: application/hal+json",
            "Authorization: Bearer $this->reverbAuth",
            "Accept: application/hal+json",
            "Accept-Version: 3.0"
        ];
        return $headers;
    }

    public static function setCurlOptions($url, $method, $headers, $post_string = null){
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_HEADER, false);
        if($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        return $request;
    }

    public function reverbCurl($url, $method, $post_string)
    {
        $headers = $this->createHeader();
        $request = ReverbClientCurl::setCurlOptions($url, $method, $headers, $post_string);
        $response = Ecommerce::curlRequest($request);
        return $response;
    }
}