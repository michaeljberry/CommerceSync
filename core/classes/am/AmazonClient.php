<?php

namespace am;

use models\channels\ChannelModel;
use ecommerce\EcommerceInterface;

class AmazonClient implements EcommerceInterface
{

    use AmazonClientCurl;

    private $am_merchant_id;
    private $am_marketplace_id;
    private $am_aws_access_key;
    private $am_secret_key;
    public $am_store_id;
    private $aminfo;
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

        $this->aminfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setMerchantID()
    {
        $this->am_merchant_id = decrypt($this->aminfo['merchantid']);
    }

    private function setMarketplaceID()
    {
        $this->am_marketplace_id = decrypt($this->aminfo['marketplaceid']);
    }

    private function setAWSAccessKey()
    {
        $this->am_aws_access_key = decrypt($this->aminfo['aws_access_key']);
    }

    private function setSecretKey()
    {
        $this->am_secret_key = decrypt($this->aminfo['secret_key']);
    }

    private function setStoreID()
    {
        $this->am_store_id = $this->aminfo['store_id'];
    }

    public function getMerchantID()
    {
        return $this->am_merchant_id;
    }

    public function getMarketplaceID()
    {
        return $this->am_marketplace_id;
    }

    public function getAWSAccessKey()
    {
        return $this->am_aws_access_key;
    }

    public function getSecretKey()
    {
        return $this->am_secret_key;
    }

    public function getStoreID()
    {
        return $this->am_store_id;
    }

}