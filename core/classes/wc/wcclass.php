<?php

namespace wc;

use Crypt;
use connect\DB;
use ecommerceclass\ecommerceclass as ecom;
use Automattic\WooCommerce\Client;

class woocommerceclass{
    public $db;
    protected $wc_consumer_key;
    protected $wc_secret_key;
    protected $wc_site;
    public $wc_store_id;

    public function __construct($user_id){
        $this->db = DB::instance();
        $wcinfo = $this->getAppId($user_id);
        $this->wc_consumer_key = Crypt::decrypt($wcinfo['consumer_key']);
        $this->wc_secret_key = Crypt::decrypt($wcinfo['consumer_secret']);
        $this->wc_site = $wcinfo['site'];
        $this->wc_store_id = $wcinfo['store_id'];
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
            $this->wc_site,
            $this->wc_consumer_key, //ck_bfb1c02513b3c6ed5788d5ceb937b252d12f5c72
            $this->wc_secret_key, //cs_d4ff45e502bcacb498c4eb8ac671a24f1569a39c
            $options
        );
        return $woocommerce;
    }
    public function sanitize_column_name($col){
        switch($col){
            case $col == "consumer_key":
                $column = 'consumer_key';
                break;
            case $col == "consumer_secret":
                $column = 'consumer_secret';
                break;
        }
        return $column;
    }
    public function get_all_apps($user_id){
        $query = $this->db->prepare("SELECT store.id, store.name FROM store INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'WooCommerce'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetchAll();
    }
    public function getAppId($user_id){
        $query = $this->db->prepare("SELECT store_id, consumer_key, consumer_secret, site FROM api_wc INNER JOIN store ON api_wc.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'WooCommerce'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
    public function saveAppInfo($crypt, $store_id, $consumer_key, $consumer_secret){
        $query = $this->db->prepare("INSERT INTO api_wc (store_id, consumer_key, consumer_secret) VALUES (:store_id, :consumer_key, :consumer_secret)");
        $query_params = array(
            ":store_id" => $store_id,
            ":consumer_key" => $crypt->encrypt($consumer_key),
            ":consumer_secret" => $crypt->encrypt($consumer_secret)
        );
        $query->execute($query_params);
        return true;
    }
    public function updateAppInfo($crypt, $store_id, $column, $id){
        $column = $this->sanitize_column_name($column);
        $query = $this->db->prepare("UPDATE api_wc SET $column = :id WHERE store_id = :store_id");
        $query_params = array(
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        );
        $query->execute($query_params);
        return true;
    }
    public function isVariation($sku){
        $query = $this->db->prepare("SELECT variations FROM listing_wc WHERE sku = :sku");
        $query_params = [
            ':sku' => $sku
        ];
        $query->execute($query_params);
        return $query->fetchColumn();
    }

    protected function createHeader($method, $post_string)
    {
        $headers = [
            'Content-Type: application/json'
        ];
        if($method === 'POST' || $method === 'PUT'){
            $headers[] = 'Content-Length: ' . strlen($post_string);
        }
        return $headers;
    }

    protected function setCurlOptions($url, $method, $post_string)
    {
        $request = curl_init($url);
        $headers = $this->createHeader($method, $post_string);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_USERPWD, $this->wc_consumer_key . ":" . $this->wc_secret_key);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        if($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        return $request;
    }

    public function woocommerceCurl($url, $method, $post_string = null)
    {
        $request = $this->setCurlOptions($url, $method, $post_string);
        $response = ecom::curlRequest($request);
        return $response;
    }
}