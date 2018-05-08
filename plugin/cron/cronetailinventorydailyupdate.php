<?php

use Etail\{EtailSSH, EtailInventoryDaily};

require '../../core/init.php';

$etailInventory = new EtailInventoryDaily();

print_r($etailInventory);