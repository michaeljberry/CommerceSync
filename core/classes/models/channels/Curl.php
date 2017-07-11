<?php

namespace models\channels;


class Curl
{

    public static function request($request)
    {
        return Curl::send($request);
    }

    protected static function send($request)
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