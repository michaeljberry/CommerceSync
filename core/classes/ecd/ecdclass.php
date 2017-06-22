<?php

namespace ecd;

use models\ModelDB as MDB;

class ecdclass
{
    public function get_ecd_app_info($user_id)
    {
        $sql = "SELECT ecd.id, store_id, ecd.ocp_apim_sub_key, ecd.ecd_sub_key FROM api_ecd AS ecd INNER JOIN store ON ecd.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'EcomDash'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return MDB::query($sql, $query_params, 'fetch');
    }

    public function save_app_info($crypt, $store_id, $ocp_apim_sub_key, $ecd_sub_key)
    {
        $sql = "INSERT INTO api_ecd (store_id, ocp_apim_sub_key, ecd_sub_key) VALUES (:store_id, :ocp_apim_sub_key, :ecd_sub_key)";
        $query_params = array(
            ":store_id" => $store_id,
            ":ocp_apim_sub_key" => $crypt->encrypt($ocp_apim_sub_key),
            ":ecd_sub_key" => $crypt->encrypt($ecd_sub_key)
        );
        MDB::query($sql, $query_params);
    }

    public function curl_post($ecd_ocp_key, $ecd_sub_key, $url, $parameters)
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [
            "Content-Type: application/json",
            "Ocp-Apim-Subscription-Key: $ecd_ocp_key",
            "ecd-subscription-key: $ecd_sub_key"
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            return $response;
        }
        curl_close($ch);
    }

    public function wait_call($response, $ecd_ocp_key, $ecd_sub_key, $url, $parameters, $ecommerce, $method = 'post')
    {
        $responseJson = json_decode($response, true);
        if (isset($responseJson['statusCode']) && $responseJson['statusCode'] == '429') {
            $time = $ecommerce->substring_between($responseJson['message'], 'Try again in ', ' seconds');
            echo "Seconds to wait: $time";
            sleep($time);
            if ($method == 'post') {
                $response = $this->curl_post_update($ecd_ocp_key, $ecd_sub_key, $url, $parameters);
            }
        }
        return $response;
    }

    public function get_warehouse_ids($ecd_id)
    {
        $sql = "SELECT id, warehouse_id, warehouse_name FROM api_ecd_warehouses WHERE ecd_id = :ecd_id";
        $query_params = [
            ":ecd_id" => $ecd_id
        ];
        return MDB::query($sql, $query_params, 'fetchAll');
    }
}