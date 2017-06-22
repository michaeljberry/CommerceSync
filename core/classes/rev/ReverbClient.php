<?php

namespace rev;

use models\channels\ChannelModel;
use Crypt;
use ecommerce\EcommerceInterface;

class ReverbClient implements EcommerceInterface
{
    protected $reverbAuth;
    public $reverbStoreID;
    private $reverbInfo;

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
        $this->reverbAuth = Crypt::decrypt($this->reverbInfo['reverb_auth_token']);
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

    public function get_auth($email, $password){
        $url = 'https://reverb.com/api/auth/email';
        $post_string = '{"email":"' . $email . '","password":"' . $password . '"}';
        $response = $this->reverbCurl($url, 'POST', $post_string);
        return $response;
    }

    protected function createHeader()
    {
        $headers = [
            "Content-type: application/hal+json",
            "Authorization: Bearer $this->reverb_auth",
            "Accept: application/hal+json",
            "Accept-Version: 3.0"
        ];
        return $headers;
    }

    protected function setCurlOptions($url, $method, $headers, $post_string = null){
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_HEADER, false);
        if($post_string) {
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        }
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        return $request;
    }

    public function reverbCurl($url, $method, $post_string)
    {
        $headers = $this->createHeader();
        $request = $this->setCurlOptions($url, $method, $headers, $post_string);
        $response = curl_exec($request);
        return $response;
    }
}