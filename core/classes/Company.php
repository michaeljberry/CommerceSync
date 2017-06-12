<?php

use connect\DB;

class Company
{
    private $db;
    public function __construct(){
        $this->db = DB::instance();
    }
    //Rackspace Region Codes:
    //Northern Virginia - IAD
    //Chicago - ORD
    //Dallas - DFW
    //London - LON
    //Hong Kong - HKG
    //Sydney - SYD

    public function save_rs_info($e, $c, $company_id, $username, $api_key, $container, $ssl, $region, $api_version_url){
        $query = "UPDATE company SET rs_username = :rs_username, rs_api_key = :rs_api_key, rs_container = :rs_container, rs_ssl_file_paths = :rs_ssl_file_paths, rs_region = :rs_region, rs_api_version_url = :rs_api_version_url WHERE id = :id";
        $query_params = array(
            ":rs_username" => $username,
            ":rs_api_key" => $c->encrypt($api_key),
            ":rs_container" => $container,
            ":rs_ssl_file_paths" => $ssl,
            ":rs_region" => $region,
            ":rs_api_version_url" => $api_version_url,
            ":id" => $company_id
        );
        $company_id = $e->insert_transact($query, $query_params);
        return $company_id;
    }
    public function get_rs_info($company_id){
        $query = $this->db->prepare("SELECT rs_username, rs_api_key, rs_container, rs_ssl_file_paths, rs_region, rs_api_version_url FROM company WHERE id = :id");
        $query_params = array(
            ":id" => $company_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
}