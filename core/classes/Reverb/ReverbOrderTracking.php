<?php

namespace Reverb;

use controllers\channels\order\ChannelOrderTracking;
use controllers\channels\order\ChannelTracking;

class ReverbOrderTracking extends ChannelOrderTracking
{

    public function updateTracking(ChannelTracking $reverbTracking, ChannelOrderTracking $reverbOrderTracking)
    {
        $url = 'https://reverb.com/api/my/orders/selling/' . $reverbOrderTracking->getOrderNumber() . '/ship';
        $postString = [
            'id' => $reverbOrderTracking->getOrderNumber(),
            'provider' => $reverbOrderTracking->getCarrier(),
            'tracking_number' => $reverbOrderTracking->getTrackingNumber(),
            'send_notification' => false,
        ];
//        $response = ReverbClient::reverbCurl($url,'POST', json_encode($postString));
        return $response;
    }

    public function updated($response)
    {
        $successMessage = '"shipped"';
        if (strpos($response, $successMessage)) {
            return true;
        }
        return false;
    }
}