<?php

namespace eb;

use controllers\channels\ChannelController;
use Crypt;
use ecommerce\EcommerceInterface;

class EbayClient extends ChannelController implements EcommerceInterface
{
    public $eb_dev_id;
    public $eb_app_id;
    public $eb_cert_id;
    public $eb_token;
    public $eb_store_id;
    private $ebinfo;

    public function __construct($user_id){
        $this->setInfo($user_id);
        $this->setDevID();
        $this->setAppID();
        $this->setCertID();
        $this->setToken();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_ebay';
        $channel = 'Ebay';
        $columns = [
            'store_id',
            'devid',
            'appid',
            'certid',
            'token'
        ];

        $this->ebinfo = ChannelController::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setDevID()
    {
        $this->eb_dev_id = Crypt::decrypt($this->ebinfo['devid']);
    }

    private function setAppID()
    {
        $this->eb_app_id = Crypt::decrypt($this->ebinfo['appid']);
    }

    private function setCertID()
    {
        $this->eb_cert_id = Crypt::decrypt($this->ebinfo['certid']);
    }

    private function setToken()
    {
        $this->eb_token = Crypt::decrypt($this->ebinfo['token']);
    }

    private function setStoreID()
    {
        $this->eb_store_id = $this->ebinfo['store_id'];
    }

    public function getDevID()
    {
        return $this->eb_dev_id;
    }

    public function getAppID()
    {
        return $this->eb_app_id;
    }

    public function getCertID()
    {
        return $this->eb_cert_id;
    }

    public function getToken()
    {
        return $this->eb_token;
    }

    public function getStoreID()
    {
        return $this->eb_store_id;
    }
}