<?php

namespace Amazon;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;
use controllers\channels\XMLController;

class AmazonOrderTracking extends ChannelOrderTracking
{
    public function updateTracking(ChannelTracking $amazonTracking, ChannelOrderTracking $amazonOrderTracking)
    {
        $amazonTracking->updateTrackingXml(AmazonOrderTracking::updateTrackingInfo($amazonTracking, $amazonOrderTracking));
    }

    public function updateTrackingInfo(AmazonTracking $amazonTracking, AmazonOrderTracking $amazonOrderTracking)
    {
        $xml = [
            'Message' => [
                'MessageID' => $amazonTracking->getOrderCount(),
                'OrderFulfillment' => [
                    'AmazonOrderID' => $amazonOrderTracking->getOrderNumber(),
                    'FulfillmentDate' => gmdate("Y-m-d\TH:i:s\Z", time()),
                    'FulfillmentData' => [
                        'CarrierName' => $amazonOrderTracking->getCarrier(),
                        'ShipperTrackingNumber' => $amazonOrderTracking->getTrackingNumber()
                    ]
                ]
            ]
        ];
        return XMLController::makeXML($xml);
    }

    public function updated($trackingResponse)
    {
        return $trackingResponse;
    }
}
