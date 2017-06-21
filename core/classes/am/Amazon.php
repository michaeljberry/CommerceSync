<?php

namespace am;

use ecommerce\Ecommerce as ecom;
use models\ModelDB as EDB;
use models\channels\channelModel;

class Amazon
{
    protected $AmazonClient;

    public function __construct(AmazonClient $AmazonClient){
        $this->AmazonClient = $AmazonClient;
    }
    public function sanitizeColumnName($col){
        switch($col){
            case $col == "merchantid":
                $column = 'merchantid';
                break;
            case $col == "marketplaceid":
                $column = 'marketplaceid';
                break;
            case $col == "aws_access_key":
                $column = 'aws_access_key';
                break;
            case $col == "secret_key":
                $column = 'secret_key';
                break;
        }
        return $column;
    }
    //temp solution
    public function find_amazon_listing($store_listing_id){
        $sql = "SELECT id FROM listing_amazon WHERE store_listing_id = :store_listing_id";
        $query_params = array(
            'store_listing_id' => $store_listing_id
        );
        return EDB::query($sql, $query_params, 'fetchColumn');
    }
    public function get_amazon_app_id($user_id){
        $sql = "SELECT store_id, merchantid, marketplaceid, aws_access_key, secret_key FROM api_amazon INNER JOIN store ON api_amazon.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Amazon'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return EDB::query($sql, $query_params, 'fetch');
    }
    public function save_app_info($crypt, $store_id, $merchant_id, $marketplace_id, $aws_access_key, $secret_key){
        $sql = "INSERT INTO api_amazon (store_id, merchantid, marketplaceid, aws_access_key, secret_key) VALUES (:store_id, :merchantid, :marketplaceid, :aws_access_key, :secret_key)";
        $query_params = array(
            ":store_id" => $store_id,
            ":merchantid" => $crypt->encrypt($merchant_id),
            ":marketplaceid" => $crypt->encrypt($marketplace_id),
            ":aws_access_key" => $crypt->encrypt($aws_access_key)
        );
        EDB::query($sql, $query_params);
    }
    public function update_app_info($crypt, $store_id, $column, $id){
        $column = $this->sanitizeColumnName($column);
        $sql = "UPDATE api_amazon SET $column = :id WHERE store_id = :store_id";
        $query_params = array(
            ':id' => $crypt->encrypt($id),
            ':store_id' => $store_id
        );
        EDB::query($sql, $query_params);
    }
    public function get_order_dates($store_id){
        $sql = "SELECT api_pullfrom, api_pullto FROM api_amazon WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => $store_id
        ];
        return EDB::query($sql, $query_params, 'fetch');
    }
    public function set_order_dates($store_id, $from, $to){
        $sql = "UPDATE api_amazon SET api_pullfrom = :api_pullfrom, api_pullto = :api_pullto WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => $store_id,
            ':api_pullfrom' => $from,
            ':api_pullto' => $to
        ];
        EDB::query($sql, $query_params);
    }

}