<?php

namespace Etail\Tracking;

class EtailTrackingThirtyMinutes extends EtailTrackingUpdate
{

    protected $interval = 30;

    public function getInterval()
    {

        return $this->interval;

    }

}