<?php

namespace Etail\Inventory;

use CSV\CSV;

class EtailInventoryFiveMinute extends EtailInventoryUpdate
{

    protected $interval = 15;

    public function getInterval()
    {

        return $this->interval;

    }

}