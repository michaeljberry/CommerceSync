<?php

namespace controllers\channels\order;


use models\channels\Channel;

abstract class ChannelTracking
{
    protected $channelName;
    protected $channelNumbers;
    protected $orders;

    public function __construct($channelName)
    {
        $this->setChannelName($channelName);
        $this->setChannelNumbers();
    }

    private function setChannelName($channelName)
    {
        $this->channelName = $channelName;
    }

    private function setChannelNumbers()
    {
        $this->channelNumbers = Channel::getAccountNumbers($this->channelName);
    }

    public function getChannelName()
    {
        return $this->channelName;
    }

    public function getChannelNumbers()
    {
        return $this->channelNumbers;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getOrder($orderNum)
    {
        return $this->orders[$orderNum];
    }

    public function updateOrders(ChannelOrderTracking $order)
    {
        $this->orders[$order->getOrderNumber()] = $order;
    }
}