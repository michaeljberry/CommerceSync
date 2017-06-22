<?php

namespace eb;

use models\channels\ChannelModel;
use ecommerce\EcommerceInterface;

class EbayClient implements EcommerceInterface
{

    use EbayClientCurl;

    protected $eBayDevID;
    protected $eBayAppID;
    protected $eBayCertID;
    protected $eBayToken;
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

        $this->ebinfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setDevID()
    {
        $this->eBayDevID = decrypt($this->ebinfo['devid']);
    }

    private function setAppID()
    {
        $this->eBayAppID = decrypt($this->ebinfo['appid']);
    }

    private function setCertID()
    {
        $this->eBayCertID = decrypt($this->ebinfo['certid']);
    }

    private function setToken()
    {
        $this->eBayToken = decrypt($this->ebinfo['token']);
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