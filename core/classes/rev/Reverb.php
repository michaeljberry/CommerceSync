<?php

namespace rev;

use Crypt;
use models\ModelDB as MDB;
use ecommerce\Ecommerce as ecom;

class Reverb
{
    protected $ReverbClient;

    public function __construct(ReverbClient $reverbClient){
        $this->ReverbClient = $reverbClient;
    }
    public function get_reverb_app_id($user_id){
        $sql = "SELECT store_id, reverb_email, reverb_pass, reverb_auth_token FROM api_reverb INNER JOIN store ON api_reverb.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Reverb'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return MDB::query($sql, $query_params, 'fetch');
    }
    public function save_app_info($store_id, $reverb_auth_token){
        $sql = "INSERT INTO api_reverb (store_id, reverb_auth_token) VALUES (:store_id, :reverb_auth_token) ON DUPLICATE KEY UPDATE reverb_auth_token = :reverb_auth_token2";
        $query_params = array(
            ":store_id" => $store_id,
            ":reverb_auth_token"  => Crypt::encrypt($reverb_auth_token),
            ":reverb_auth_token2"  => Crypt::encrypt($reverb_auth_token),
        );
        MDB::query($sql, $query_params);
    }

}