<?php

namespace Etail\Tracking;

class EtailTrackingSixtyMinutes extends EtailTrackingUpdate
{

    protected $interval = 60;

    public function getInterval()
    {

        return $this->interval;

    }

}