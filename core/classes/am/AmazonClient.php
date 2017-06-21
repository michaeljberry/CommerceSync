<?php

namespace am;

use controllers\channels\ChannelController;
use Crypt;
use ecommerce\EcommerceInterface;
use ecommerce\ecommerce as ecom;

class AmazonClient extends ChannelController implements EcommerceInterface
{
    private $am_merchant_id;
    private $am_marketplace_id;
    private $am_aws_access_key;
    private $am_secret_key;
    public $am_store_id;
    private $aminfo;

    public function __construct($user_id){
        $this->setInfo($user_id);
        $this->setMerchantID();
        $this->setMarketplaceID();
        $this->setAWSAccessKey();
        $this->setSecretKey();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_amazon';
        $channel = 'Amazon';
        $columns = [
            'store_id',
            'merchantid',
            'marketplaceid',
            'aws_access_key',
            'secret_key'
        ];

        $this->aminfo = self::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setMerchantID()
    {
        $this->am_merchant_id = Crypt::decrypt($this->aminfo['merchantid']);
    }

    private function setMarketplaceID()
    {
        $this->am_marketplace_id = Crypt::decrypt($this->aminfo['marketplaceid']);
    }

    private function setAWSAccessKey()
    {
        $this->am_aws_access_key = Crypt::decrypt($this->aminfo['aws_access_key']);
    }

    private function setSecretKey()
    {
        $this->am_secret_key = Crypt::decrypt($this->aminfo['secret_key']);
    }

    private function setStoreID()
    {
        $this->am_store_id = Crypt::decrypt($this->aminfo['store_id']);
    }

    public function getMerchantID()
    {
        return $this->am_merchant_id;
    }
    public function getMarketplaceID()
    {
        return $this->am_merchant_id;
    }
    public function getAWSAccessKey()
    {
        return $this->am_merchant_id;
    }
    public function getSecretKey()
    {
        return $this->am_merchant_id;
    }
    public function getStoreID()
    {
        return $this->am_merchant_id;
    }

    public function getDBInstance()
    {
        return $this->db;
    }
}