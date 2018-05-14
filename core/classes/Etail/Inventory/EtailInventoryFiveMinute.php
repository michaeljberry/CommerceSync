<?php

namespace Etail\Inventory;

use CSV\CSV;

class EtailInventoryFiveMinute extends EtailInventoryUpdate
{

    protected $interval = 5;

    public function __construct()
    {

        parent::__construct($this->getInterval());

    }

    public function getInterval()
    {

        return $this->interval;

    }

}