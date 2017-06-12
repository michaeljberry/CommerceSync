<?php

namespace rev;

use Crypt;
use connect\DB;
use ecommerceclass\ecommerceclass as ecom;

class reverbclass
{
    public $db;
    protected $reverb_email;
    protected $reverb_password;
    protected $reverb_auth;
    public $reverb_store_id;

    public function __construct($user_id){
        $this->db = DB::instance();
        $reverbinfo = $this->get_reverb_app_id($user_id);
        $this->reverb_email = $reverbinfo['reverb_email'];
        $this->reverb_password = Crypt::decrypt($reverbinfo['reverb_pass']);
        $this->reverb_auth = Crypt::decrypt($reverbinfo['reverb_auth_token']);
        $this->reverb_store_id = $reverbinfo['store_id'];
//        ecom::dd($this->reverb_auth);
    }
    public function get_reverb_app_id($user_id){
        $query = $this->db->prepare("SELECT store_id, reverb_email, reverb_pass, reverb_auth_token FROM api_reverb INNER JOIN store ON api_reverb.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Reverb'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
    public function save_app_info($store_id, $reverb_auth_token){
        $query = $this->db->prepare("INSERT INTO api_reverb (store_id, reverb_auth_token) VALUES (:store_id, :reverb_auth_token) ON DUPLICATE KEY UPDATE reverb_auth_token = :reverb_auth_token2");
        $query_params = array(
            ":store_id" => $store_id,
            ":reverb_auth_token"  => Crypt::encrypt($reverb_auth_token),
            ":reverb_auth_token2"  => Crypt::encrypt($reverb_auth_token),
        );
        $query->execute($query_params);
        return true;
    }
    public function get_auth($email, $password){
        $url = 'https://reverb.com/api/auth/email';
        $post_string = '{"email":"' . $email . '","password":"' . $password . '"}';
        $response = $this->reverbCurl($url, 'POST', $post_string);
        return $response;
    }

    protected function createHeader()
    {
        $headers = [
            "Content-type: application/hal+json",
            "Authorization: Bearer $this->reverb_auth",
            "Accept: application/hal+json",
            "Accept-Version: 3.0"
        ];
        return $headers;
    }

    protected function setCurlOptions($url, $method, $headers, $post_string = null){
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_HEADER, false);
        if($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        return $request;
    }

    public function reverbCurl($url, $method, $post_string)
    {
        $headers = $this->createHeader();
        $request = $this->setCurlOptions($url, $method, $headers, $post_string);
        $response = curl_exec($request);
        return $response;
    }
}