<?php

namespace Etail;

class EtailInventoryFiveMinute extends EtailInventory
{

    public function __construct()
    {

        parent::__construct();

        $this->compareNewInventoryLevelsWithPreviousLevels();

    }

}