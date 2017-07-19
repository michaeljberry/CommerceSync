<?php

error_reporting(-1);

require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'wm/wmvar.php';

$start = startClock();
$user_id = 838;

$wmorder = $wmord->configure();

$wmord->getOrders($wmorder, $wmord);

endClock($start);