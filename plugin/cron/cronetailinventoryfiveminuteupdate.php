<?php

use Etail\{EtailSSH, EtailInventoryFiveMinute};

require __DIR__  . '/../../core/init.php';

$etailInventory = new EtailInventoryFiveMinute();

print_r($etailInventory);