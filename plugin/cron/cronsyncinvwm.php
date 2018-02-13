<?php

use Walmart\WalmartInventory;

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'wm/wmvar.php';

$start = startClock();

print_r(WalmartInventory::getAllItems());

endClock($start);
