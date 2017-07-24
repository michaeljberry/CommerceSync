<?php

namespace Amazon;

use controllers\channels\order\OrderTracking;

class AmazonOrderTracking implements OrderTracking
{
    private $orderNumber;
    private $trackingNumber;
    private $carrier;
    private $shipped;
    private $success;
    private $orderCount;

    public function __construct($orderNumber, $trackingNumber, $carrier)
    {
        $this->setOrderNumber($orderNumber);
        $this->setTrackingNumber($trackingNumber);
        $this->setCarrier($carrier);
        $this->setOrderCount();
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

    private function setOrderCount()
    {
        $this->orderCount = 0;
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

    public function getOrderCount()
    {
        $this->updateOrderCount();
        return $this->orderCount;
    }

    protected function updateOrderCount()
    {
        $this->orderCount++;
    }
}