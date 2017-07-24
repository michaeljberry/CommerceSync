<?php

namespace Amazon;

use controllers\channels\order\OrderTracking;
use models\channels\Channel;

class AmazonTracking
{
    private $channelName;
    private $channelNumbers;
    private $trackingXML;
    private $orders;

    public function __construct($channelName)
    {
        $this->setChannelName($channelName);
        $this->setChannelNumbers();
        $this->setTrackingXml();
    }

    private function setChannelName($channelName)
    {
        $this->channelName = $channelName;
    }

    private function setChannelNumbers()
    {
        $this->channelNumbers = Channel::getAccountNumbers($this->channelName);
    }

    private function setTrackingXml()
    {
        $this->trackingXML = '';
    }

    public function getChannelName()
    {
        return $this->channelName;
    }

    public function getChannelNumbers()
    {
        return $this->channelNumbers;
    }

    public function getTrackingXml()
    {
        return $this->trackingXML;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function updateTrackingXml($trackingXML)
    {
        $this->trackingXML .= $trackingXML;
    }

    public function updateOrders(OrderTracking $order)
    {
        $this->orders[$order->getOrderNumber()] = $order;
    }
}