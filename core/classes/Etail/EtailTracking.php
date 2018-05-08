<?php

namespace Etail;

class EtailTracking
{

    public function __construct()
    {

        $this->getUnshippedOrders();

        $this->getTrackingForOrder($orderNumber);

        $this->formatTrackingForUpload();

        $this->uploadTracking();

    }

}