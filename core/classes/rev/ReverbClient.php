<?php

namespace rev;

use ecommerce\EcommerceInterface;
use models\channels\ChannelModel;

class ReverbClient implements EcommerceInterface
{

    use ReverbClientCurl;

    private $reverbInfo;
    private $reverbAuth;
    public $reverbStoreID;

    public function __construct($user_id)
    {
        $this->setInfo($user_id);
        $this->setAuthToken();
        $this->setStoreID();
    }

    private function setInfo($user_id)
    {
        $table = 'api_reverb';
        $channel = 'Reverb';
        $columns = [
            'reverb_email',
            'reverb_pass',
            'reverb_auth_token',
            'store_id'
        ];

        $this->reverbInfo = ChannelModel::getAppInfo($user_id, $table, $channel, $columns);
    }

    private function setAuthToken()
    {
        $this->reverbAuth = decrypt($this->reverbInfo['reverb_auth_token']);
    }

    private function setStoreID()
    {
        $this->reverbStoreID = $this->reverbInfo['store_id'];
    }

    public function getAuthToken()
    {
        return $this->reverbAuth;
    }

    public function getStoreID()
    {
        return $this->reverbStoreID;
    }

}