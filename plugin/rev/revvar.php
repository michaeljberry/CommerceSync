<?php
$reverbClient = \rev\ReverbClient::instance($userID);
$reverb = new \rev\Reverb($reverbClient);
$revord = new \rev\ReverbOrder($reverbClient);
$revinv = new \rev\ReverbInventory($reverbClient);