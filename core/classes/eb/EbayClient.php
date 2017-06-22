<?php

namespace eb;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class EbayClient implements EcommerceInterface
{

    use EbayClientCurl;

    private $ebayInfo;
    private $eBayDevID;
    private $eBayAppID;
    private $eBayCertID;
    private $eBayToken;
    public $eBayStoreID;

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

        $this->ebayInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setDevID()
    {
        $this->eBayDevID = decrypt($this->ebayInfo['devid']);
    }

    private function setAppID()
    {
        $this->eBayAppID = decrypt($this->ebayInfo['appid']);
    }

    private function setCertID()
    {
        $this->eBayCertID = decrypt($this->ebayInfo['certid']);
    }

    private function setToken()
    {
        $this->eBayToken = decrypt($this->ebayInfo['token']);
    }

    private function setStoreID()
    {
        $this->eBayStoreID = $this->ebayInfo['store_id'];
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