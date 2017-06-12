<?php

namespace bc;

use Crypt;
use connect\DB;

class bcclient
{
    public $db;
    private $bcinfo;
    protected $bc_store_url;
    protected $bc_username;
    protected $bc_api_key;
    public $bc_store_id;


    public function __construct($user_id){
        $this->setDBInstance();
        $this->setBCInfo($user_id);
        $this->setBCStoreUrl();
        $this->setBCUsername();
        $this->setBCAPIKey();
        $this->setBCStoreId();
    }
    public function get_bc_app_info($user_id){
        $query = $this->db->prepare("SELECT store_id, store_url, bc.username, api_key FROM api_bigcommerce AS bc INNER JOIN store ON bc.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'BigCommerce'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }

    private function setDBInstance()
    {
        $this->db = DB::instance();
    }

    private function setBCInfo($user_id)
    {
        $this->bcinfo = $this->get_bc_app_info($user_id);
    }

    private function setBCStoreUrl()
    {
        $this->bc_store_url = $this->bcinfo['store_url'];
    }

    private function setBCUsername()
    {
        $this->bc_username = Crypt::decrypt($this->bcinfo['username']);
    }

    private function setBCAPIKey()
    {
        $this->bc_api_key = Crypt::decrypt($this->bcinfo['api_key']);
    }

    private function setBCStoreId()
    {
        $this->bc_store_id = $this->bcinfo['store_id'];
    }
    public function getDBInstance()
    {
        return $this->db;
    }
    public function getBCStoreUrl()
    {
        return $this->bc_store_url;
    }

    public function getBCUsername()
    {
        return $this->bc_username;
    }

    public function getBCAPIKey()
    {
        return $this->bc_api_key;
    }

    public function getBCStoreId()
    {
        return $this->bc_store_id;
    }
}