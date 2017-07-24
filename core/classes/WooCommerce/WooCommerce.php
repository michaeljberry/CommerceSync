<?php

namespace WooCommerce;

use Crypt;
use models\ModelDB as MDB;
use ecommerce\Ecommerce;
use Automattic\WooCommerce\Client;

class WooCommerce
{
    public function __construct()
    {
        $this->configure();
    }

    public function configure()
    {
        $options = [
            'wp_json' => true,
            //    'version' => 'wc/v1',
            //    'version' => 'v3',
            //    'query_string_auth' => true
        ];

        $woocommerce = new Client(
            WooCommerceClient::getSite(),
            WooCommerceClient::getConsumerKey(), //ck_bfb1c02513b3c6ed5788d5ceb937b252d12f5c72
            WooCommerceClient::getSecretKey(), //cs_d4ff45e502bcacb498c4eb8ac671a24f1569a39c
            $options
        );
        return $woocommerce;
    }

    public function sanitize_column_name($col)
    {
        switch ($col) {
            case $col == "consumer_key":
                $column = 'consumer_key';
                break;
            case $col == "consumer_secret":
                $column = 'consumer_secret';
                break;
        }
        return $column;
    }

    public function get_all_apps($user_id)
    {
        $sql = "SELECT store.id, store.name FROM store INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'WooCommerce'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return MDB::query($sql, $query_params, 'fetchAll');
    }

    public function getAppId($user_id)
    {
        $sql = "SELECT store_id, consumer_key, consumer_secret, site FROM api_wc INNER JOIN store ON api_wc.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'WooCommerce'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return MDB::query($sql, $query_params, 'fetch');
    }

    public function saveAppInfo($crypt, $store_id, $consumer_key, $consumer_secret)
    {
        $sql = "INSERT INTO api_wc (store_id, consumer_key, consumer_secret) VALUES (:store_id, :consumer_key, :consumer_secret)";
        $query_params = array(
            ":store_id" => $store_id,
            ":consumer_key" => $crypt->encrypt($consumer_key),
            ":consumer_secret" => $crypt->encrypt($consumer_secret)
        );
        MDB::query($sql, $query_params);
    }

    public function updateAppInfo($crypt, $store_id, $column, $id)
    {
        $column = $this->sanitize_column_name($column);
        $sql = "UPDATE api_wc SET $column = :id WHERE store_id = :store_id";
        $query_params = array(
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        );
        MDB::query($sql, $query_params);
    }

    public function isVariation($sku)
    {
        $sql = "SELECT variations FROM listing_wc WHERE sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

}