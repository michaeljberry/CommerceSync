<?php

namespace eb;

use controllers\channels\ChannelController;
use Crypt;
use ecommerce\EcommerceInterface;

class EbayClient extends ChannelController implements EcommerceInterface
{
    public $eBayDevID;
    public $eBayAppID;
    public $eBayCertID;
    public $eBayToken;
    public $eBayStoreID;
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
        $this->eBayDevID = Crypt::decrypt($this->ebinfo['devid']);
    }

    private function setAppID()
    {
        $this->eBayAppID = Crypt::decrypt($this->ebinfo['appid']);
    }

    private function setCertID()
    {
        $this->eBayCertID = Crypt::decrypt($this->ebinfo['certid']);
    }

    private function setToken()
    {
        $this->eBayToken = Crypt::decrypt($this->ebinfo['token']);
    }

    private function setStoreID()
    {
        $this->eBayStoreID = $this->ebinfo['store_id'];
    }

    public function getDevID()
    {
        return $this->eBayDevID;
    }

    public function getAppID()
    {
        return $this->eBayAppID;
    }

    public function getCertID()
    {
        return $this->eBayCertID;
    }

    public function getToken()
    {
        return $this->eBayToken;
    }

    public function getStoreID()
    {
        return $this->eBayStoreID;
    }

}