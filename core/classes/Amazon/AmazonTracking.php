<?php

namespace Amazon;

use controllers\channels\order\ChannelTracking;

class AmazonTracking extends ChannelTracking
{

    private $trackingXML;
    private $orderCount;

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

    public function updateTrackingXml($trackingXML)
    {
        $this->trackingXML .= $trackingXML;
    }

    protected function updateOrderCount()
    {
        $this->orderCount++;
    }
}