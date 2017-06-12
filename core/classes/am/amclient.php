<?php


namespace am;

use Crypt;
use connect\DB;

class amclient
{
    public $db;
    protected $am_merchant_id;
    protected $am_marketplace_id;
    protected $am_aws_access_key;
    protected $am_secret_key;
    public $am_store_id;

    public function __construct($user_id){
        $this->db = DB::instance();
        $amazonappid = $this->get_amazon_app_id($user_id);
        $this->am_merchant_id = Crypt::decrypt($amazonappid['merchantid']);
        $this->am_marketplace_id = Crypt::decrypt($amazonappid['marketplaceid']);
        $this->am_aws_access_key = Crypt::decrypt($amazonappid['aws_access_key']);
        $this->am_secret_key = Crypt::decrypt($amazonappid['secret_key']);
        $this->am_store_id = $amazonappid['store_id'];
        //        ecom::dd($this->am_merchant_id);
        //        ecom::dd($this->am_marketplace_id);
        //        ecom::dd($this->am_aws_access_key);
        //        ecom::dd($this->am_secret_key);
    }

    public function getAmazonMerchantID()
    {
        return $this->am_merchant_id;
    }
    public function getAmazonMarketplaceID()
    {
        return $this->am_merchant_id;
    }
    public function getAmazonAWSAccessKey()
    {
        return $this->am_merchant_id;
    }
    public function getAmazonSecretKey()
    {
        return $this->am_merchant_id;
    }
    public function getAmazonStoreID()
    {
        return $this->am_merchant_id;
    }

    public function getDBInstance()
    {
        return $this->db;
    }
    public function get_amazon_app_id($user_id){
        $query = $this->db->prepare("SELECT store_id, merchantid, marketplaceid, aws_access_key, secret_key FROM api_amazon INNER JOIN store ON api_amazon.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Amazon'");
        $query_params = array(
            ':user_id' => $user_id
        );
        $query->execute($query_params);
        return $query->fetch();
    }
}