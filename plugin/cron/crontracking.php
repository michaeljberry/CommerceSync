<?php

error_reporting(-1);
require __DIR__ . '/../../core/init.php';
require WEBCORE . 'ibminit.php';
require WEBPLUGIN . 'am/amvar.php';
require WEBPLUGIN . 'bc/bcvar.php';
require WEBPLUGIN . 'eb/ebvar.php';
require WEBPLUGIN . 'rev/revvar.php';
require WEBPLUGIN . 'wm/wmvar.php';

use controllers\channels\TrackingController;

//ob_start();

$start = startClock();
$userID = 838;

$amazon_throttle = false;

$folder = ROOTFOLDER;
$logFileName = 'Tracking - ' . date('ymd') . '.txt';
$trackingLog = $folder . 'log/tracking/' . $logFileName;
echo "Tracking Numbers" . PHP_EOL;
echo "Channel -> Order Num : Tracking Number<br><br>" . PHP_EOL;

TrackingController::updateTracking();

endClock($start);
//$content = ob_get_contents();
//ob_end_clean();
//file_put_contents($inventory_log, $content, FILE_APPEND);
