<?php

namespace ecdinv;

use ecd\ecdclass;

class ecdinvclass extends ecdclass
{
    public function update_ecd_inventory($ecd_ocp_key, $ecd_sub_key, $parameters, $ecommerce)
    {
        $url = "https://ecomdash.azure-api.net/api/inventory/updateQuantityOnHand";
        // echo $ecd_ocp_key . "<br>" . $ecd_sub_key . "<br>" . $url . "<br>" . $parameters . "<br>";
        $response = $this->curl_post($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
        // $responseJson = json_decode($response, true);
        // print_r($responseJson);
        $response = $this->wait_call($response, $ecd_ocp_key, $ecd_sub_key, $url, $parameters, $ecommerce);
//        if($responseJson['statusCode'] == '429'){
//            $time = $ecommerce->substring_between($responseJson['message'], 'Try again in ', ' seconds');
//            echo "Seconds to wait: $time";
//            sleep($time);
//            $response = $this->curl_post_update($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
//        }
        return $response;
    }
}