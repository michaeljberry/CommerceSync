<?php

namespace bc;

use ecommerce\Ecommerce as ecom;

trait BigCommerceClientCurl
{
    protected function setCurlOptions($url, $method, $post_string)
    {
        $request = curl_init($url);
        if($method === 'POST' || $method === 'PUT'){
            curl_setopt($request, CURLOPT_HTTPHEADER,
                ['Content-type: application/json', 'Content-Length: ' . strlen($post_string)]
            );
        }elseif ($method === 'GET'){
            curl_setopt($request, CURLOPT_HTTPHEADER,
                ['Accept: application/json', 'Content-Length: 0']
            );
        }

        if($post_string){
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_USERPWD, $this->bigcommerceUsername . ":" . $this->bigcommerceAPIKey);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        return $request;
    }

    public function bigcommerceCurl($url, $method, $post_string = null)
    {
        $request = $this->setCurlOptions($url, $method, $post_string);
        return ecom::curlRequest($request);
    }
}