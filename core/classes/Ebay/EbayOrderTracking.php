<?php

namespace Ebay;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;

class EbayOrderTracking extends ChannelOrderTracking
{
    private $itemID;
    private $transactionID;

    public function __construct($orderNumber, $orderID, $trackingNumber, $carrier)
    {
        parent::__construct($orderNumber, $orderID, $trackingNumber, $carrier);
    }

    public function updateTracking(ChannelTracking $ebayTracking, ChannelOrderTracking $ebayOrderTracking)
    {
        $requestName = 'CompleteSale';

        $xml = [
            'ItemID' => $ebayOrderTracking->getItemId(),
            'TransactionID' => $ebayOrderTracking->getTransactionId(),
            'Shipped' => 'true',
            'Shipment' =>
                [
                    'ShipmentTrackingDetails' =>
                        [
                            'ShipmentTrackingNumber' => $ebayOrderTracking->getTrackingNumber(),
                            'ShippingCarrierUsed' => $ebayOrderTracking->getCarrier()
                        ]
                ]
        ];

        $response = EbayClient::ebayCurl($requestName, $xml);
        return $response;
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

    public function updated($response)
    {
        $successMessage = 'Success';
        if (strpos($response, $successMessage)) {
            return true;
        }
        return false;
    }
}