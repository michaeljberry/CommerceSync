<?php

namespace models\channels;


class Curl
{

    public static function request($request)
    {
        return Curl::sendCurl($request);
    }

    protected static function sendCurl($request)
    {
        $response = curl_exec($request);
        if (curl_errno($request)) {
            curl_close($request);
            return 'Error: ' . curl_error($request);
        }
        curl_close($request);
        return $response;
    }
}