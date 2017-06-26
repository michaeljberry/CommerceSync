<?php
include_once '../../core/config.php';

//Reverb Class Declarations
$reverbClient = \rev\ReverbClient::instance($user_id);
$reverb = new \rev\Reverb($reverbClient);
$revord = new \rev\ReverbOrder($reverbClient);
$revinv = new \rev\ReverbInventory($reverbClient);