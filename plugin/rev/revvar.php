<?php
include_once '../../core/config.php';

//Reverb Classes
//include_once WEBCLASSES . 'rev/Reverb.php';
//include_once WEBCLASSES . 'rev/ReverbOrder.php';
//include_once WEBCLASSES . 'rev/ReverbInventory.php.php';
//include_once WEBCLASSES . 'ecommerceclass.php';

//Reverb Class Declarations
$reverbClient = new \rev\ReverbClient($user_id);
$reverb = new \rev\Reverb($reverbClient);
$revord = new \rev\ReverbOrder($reverbClient);
$revinv = new \rev\ReverbInventory($reverbClient);