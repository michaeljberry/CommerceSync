<?php

namespace bc;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class BigCommerceClient //implements EcommerceInterface
{

    use BigCommerceClientCurl;

    private $bigcommerceInfo;
    private $bigcommerceStoreUrl;
    private $bigcommerceUsername;
    private $bigcommerceAPIKey;
    public $bigcommerceStoreID;


    public function __construct($user_id){
        $this->setInfo($user_id);
        $this->setStoreUrl();
        $this->setUsername();
        $this->setAPIKey();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_bigcommerce';
        $channel = 'BigCommerce';
        $columns = [
            'store_id',
            'store_url',
            'username',
            'api_key'
        ];

        $this->bigcommerceInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setStoreUrl()
    {
        $this->bigcommerceStoreUrl = $this->bigcommerceInfo['store_url'];
    }

    private function setUsername()
    {
        $this->bigcommerceUsername = decrypt($this->bigcommerceInfo['username']);
    }

    private function setAPIKey()
    {
        $this->bigcommerceAPIKey = decrypt($this->bigcommerceInfo['api_key']);
    }

    private function setStoreID()
    {
        $this->bigcommerceStoreID = $this->bigcommerceInfo['store_id'];
    }
    public function getStoreUrl()
    {
        return $this->bigcommerceStoreUrl;
    }

    public function getUsername()
    {
        return $this->bigcommerceUsername;
    }

    public function getAPIKey()
    {
        return $this->bigcommerceAPIKey;
    }

    public function getStoreID()
    {
        return $this->bigcommerceStoreID;
    }
}