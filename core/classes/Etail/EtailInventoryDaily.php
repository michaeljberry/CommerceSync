<?php

namespace Etail;

use CSV\CSV;

class EtailInventoryDaily extends EtailInventoryUpdate
{

    protected $interval = null;

    public function __construct()
    {

        parent::__construct($this->getInterval());

    }

    public function getInterval()
    {

        return $this->interval;

    }

}