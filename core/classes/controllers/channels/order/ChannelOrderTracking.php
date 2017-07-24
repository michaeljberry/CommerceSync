<?php

namespace controllers\channels\order;


abstract class ChannelOrderTracking
{

    protected $orderNumber;
    protected $trackingNumber;
    protected $carrier;
    protected $shipped;
    private $orderID;

    abstract function updated($trackingResponse);
    abstract function updateTracking(ChannelTracking $channelTracking, ChannelOrderTracking $channelOrderTracking);

    public function __construct($orderNumber, $orderID, $trackingNumber, $carrier)
    {
        $this->setOrderNumber($orderNumber);
        $this->setOrderId($orderID);
        $this->setTrackingNumber($trackingNumber);
        $this->setCarrier($carrier);
    }

    private function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    private function setOrderId($orderID)
    {
        $this->orderID = $orderID;
    }

    private function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    private function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    public function setShipped()
    {
        $this->shipped = true;
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function getOrderId()
    {
        return $this->orderID;
    }

    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }

    public function getShipped()
    {
        return $this->shipped;
    }
}