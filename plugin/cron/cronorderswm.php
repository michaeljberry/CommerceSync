<?php
error_reporting(-1);
include __DIR__ . '/../../core/init.php';
include WEBCORE . 'ibminit.php';

$start = startClock();
$user_id = 838;
require WEBPLUGIN . 'wm/wmvar.php';

$wmorder = $wmord->configure();

$wmord->getOrders($wmorder, $wmord);

endClock($start);