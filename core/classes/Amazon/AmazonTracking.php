<?php

namespace Amazon;

use controllers\channels\order\ChannelTracking;
use controllers\channels\XMLController;
use ecommerce\Ecommerce;

class AmazonTracking extends ChannelTracking
{

    private $trackingXML;
    private $orderCount;
    private $throttleStatus = false;

    public function __construct($channelName)
    {
        parent::__construct($channelName);
        $this->setTrackingXml();
        $this->setOrderCount();
    }

    private function setTrackingXml()
    {
        $this->trackingXML = '';
    }

    private function setOrderCount()
    {
        $this->orderCount = 0;
    }

    public function getTrackingXml()
    {
        return $this->trackingXML;
    }

    public function getOrderCount()
    {
        $this->updateOrderCount();
        return $this->orderCount;
    }

    public function getThrottleStatus()
    {
        return $this->throttleStatus;
    }

    public function updateTrackingXml($trackingXML)
    {
        $this->trackingXML .= $trackingXML;
    }

    protected function updateOrderCount()
    {
        $this->orderCount++;
    }

    protected function updateThrottleStatus($throttleStatus)
    {
        $this->throttleStatus = $throttleStatus;
    }

    public function sendTracking($xml1)
    {
        $action = 'SubmitFeed';
        $feedtype = '_POST_ORDER_FULFILLMENT_DATA_';
        $feed = 'Feeds';
        $version = AmazonClient::getAPIFeedInfo($feed)['versionDate'];
        $whatToDo = 'POST';

        $paramAdditionalConfig = [
            'SellerId'
        ];

        $param = AmazonClient::setParams($action, $feedtype, $version, $paramAdditionalConfig);

        $xml = [
            'MessageType' => 'OrderFulfillment',
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;

        return AmazonClient::amazonCurl($xml, $feed, $version, $param, $whatToDo);
    }

    public function updateAmazonTracking()
    {
        if (!empty($this->getTrackingXml())) {
            Ecommerce::dd($this->getTrackingXml());
            $response = $this->sendTracking($this->getTrackingXml());
            print_r($response);
            echo '<br>';
            $successMessage = 'SUBMITTED';
//            if (strpos($response, $successMessage)) {
//                foreach ($amazonOrdersThatHaveShipped as $orderNumber) {
//                    $success = Tracking::markAsShipped($orderNumber, $channel);
//                }
//            } elseif (strpos($response, 'throttle') || strpos($response, 'QuotaExceeded')) {
//                $amazon_throttle = true;
//                echo 'Amazon is throttled.<br>';
//            }
        }
    }
}