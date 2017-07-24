<?php

namespace Ebay;

use controllers\channels\order\ChannelOrderTracking;

class EbayOrderTracking extends ChannelOrderTracking
{
    private $itemID;
    private $transactionID;

    public function __construct($orderNumber, $trackingNumber, $carrier)
    {
        parent::__construct($orderNumber, $trackingNumber, $carrier);
    }

    public function setItemId($itemID)
    {
        $this->itemID = $itemID;
    }

    public function setTransactionId($transactionID)
    {
        $this->transactionID = $transactionID;
    }

    public function getItemId()
    {
        return $this->itemID;
    }

    public function getTransactionId()
    {
        return $this->transactionID;
    }
}