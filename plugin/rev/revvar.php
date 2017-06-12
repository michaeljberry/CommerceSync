<?php
include_once '../../core/config.php';

//Reverb Classes
include_once WEBCLASSES . 'rev/revclass.php';
include_once WEBCLASSES . 'rev/revordclass.php';
include_once WEBCLASSES . 'rev/revinvclass.php';
include_once WEBCLASSES . 'ecommerceclass.php';

//Reverb Class Declarations
$reverb = new \rev\reverbclass($user_id);
$revord = new \rev\revordclass($user_id);
$revinv = new \rev\revinvclass($user_id);