<?php

namespace controllers\channels\order;


abstract class ChannelOrderTracking
{

    protected $orderNumber;
    protected $trackingNumber;
    protected $carrier;
    protected $shipped;
    protected $success;

    public function __construct($orderNumber, $trackingNumber, $carrier)
    {
        $this->setOrderNumber($orderNumber);
        $this->setTrackingNumber($trackingNumber);
        $this->setCarrier($carrier);
    }

    private function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
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

    public function setSuccess()
    {
        $this->success = true;
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
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

    public function getSuccess()
    {
        return $this->success;
    }
}