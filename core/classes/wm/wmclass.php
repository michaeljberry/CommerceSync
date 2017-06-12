<?php

namespace wm;

use connect\DB;

class walmartclass
{
    public $db;

    public function __construct()
    {
        $this->db = DB::instance();
    }
    public function get_wm_app_info($user_id){
        $query = $this->db->prepare("SELECT wm.id, store_id, wm.consumer_id, wm.secret_key, wm.api_header FROM api_walmart AS wm INNER JOIN store ON wm.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Walmart'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
    public function save_app_info($crypt, $store_id, $consumer_id, $secret_key){
        $query = $this->db->prepare("INSERT INTO api_walmart (store_id, consumer_id, secret_key) VALUES (:store_id, :consumer_id, :secret_key)");
        $query_params = array(
            ":store_id" => $store_id,
            ":consumer_id" => $crypt->encrypt($consumer_id),
            ":secret_key" => $crypt->encrypt($secret_key)
        );
        $query->execute($query_params);
        return true;
    }
    public function update_app_info($crypt, $store_id, $consumer_id, $secret_key, $api_header){
        $query = $this->db->prepare("UPDATE api_walmart SET consumer_id = :consumer_id, secret_key = :secret_key, api_header = :api_header WHERE store_id = :store_id");
        $query_params = [
            ':consumer_id' => $crypt->encrypt($consumer_id),
            ':secret_key' => $crypt->encrypt($secret_key),
            ':api_header' => $api_header,
            ':store_id' => $store_id
        ];
        $query->execute($query_params);
        return true;
    }
}