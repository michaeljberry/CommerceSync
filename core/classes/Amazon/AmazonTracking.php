<?php

namespace Amazon;

use controllers\channels\order\ChannelTracking;
use controllers\channels\XMLController;
use Ecommerce\Ecommerce;

use AmazonMWSAPI\AmazonClient;

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
        $whatToDo = 'POST';

        $paramAdditionalConfig = [
            'SellerId'
        ];

        AmazonClient::setParameters($action, $feedtype, $feed, $paramAdditionalConfig);

        $xml = [
            'MessageType' => 'OrderFulfillment',
        ];
        $xml = XMLController::makeXML($xml);
        $xml .= $xml1;

        return AmazonClient::amazonCurl($xml, $feed, $whatToDo);

    }

    public function updateAmazonTracking(ChannelTracking $tracking)
    {

        if (!empty($this->getTrackingXml())) {

            Ecommerce::dd($this->getTrackingXml());
            $response = $this->sendTracking($this->getTrackingXml());
            Ecommerce::dd($response);
            $successMessage = 'SUBMITTED';

            if (strpos($response, $successMessage)) {

                foreach($tracking->getOrders() as $order){

                    if($order->getTrackingNumber()) {

                        $order->setShipped();

                    }

                }

            } elseif (strpos($response, 'throttle') || strpos($response, 'QuotaExceeded')) {

                $this->updateThrottleStatus(true);
                echo 'Amazon is throttled.<br>';

            }

        }

    }

}
