<?php

namespace am;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class AmazonClient implements EcommerceInterface
{

    use AmazonClientCurl;

    private $amazonInfo;
    private $amazonMerchantID;
    private $amazonMarketplaceID;
    private $amazonAWSAccessKey;
    private $amazonSecretKey;
    public $amazonStoreID;

    public $apiFeedInfo = [
        'FulfillmentInventory' => [
            'versionDate' => '2010-10-01'
        ],
        'Feeds' => [
            'versionDate' => '2009-01-01'
        ],
        'Products' => [
            'versionDate' => '2011-10-01'
        ],
        'Orders' => [
            'versionDate' => '2013-09-01'
        ],
        'doc' => [
            'versionDate' => '2009-01-01'
        ]
    ];

    /**
     * AmazonClient constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->setInfo($user_id);
        $this->setMerchantID();
        $this->setMarketplaceID();
        $this->setAWSAccessKey();
        $this->setSecretKey();
        $this->setStoreID();
    }

    /**
     * @param $user_id
     */
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

        $this->amazonInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setMerchantID()
    {
        $this->amazonMerchantID = decrypt($this->amazonInfo['merchantid']);
    }

    private function setMarketplaceID()
    {
        $this->amazonMarketplaceID = decrypt($this->amazonInfo['marketplaceid']);
    }

    private function setAWSAccessKey()
    {
        $this->amazonAWSAccessKey = decrypt($this->amazonInfo['aws_access_key']);
    }

    private function setSecretKey()
    {
        $this->amazonSecretKey = decrypt($this->amazonInfo['secret_key']);
    }

    private function setStoreID()
    {
        $this->amazonStoreID = $this->amazonInfo['store_id'];
    }

    public function getMerchantID()
    {
        return $this->amazonMerchantID;
    }

    public function getMarketplaceID()
    {
        return $this->amazonMarketplaceID;
    }

    public function getAWSAccessKey()
    {
        return $this->amazonAWSAccessKey;
    }

    public function getSecretKey()
    {
        return $this->amazonSecretKey;
    }

    public function getStoreID()
    {
        return $this->amazonStoreID;
    }

}