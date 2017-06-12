<?php
include_once '../../core/config.php';

//Amazon Classes
include_once WEBCLASSES . 'am/amclient.php';
include_once WEBCLASSES . 'am/amclass.php';
include_once WEBCLASSES . 'am/amordclass.php';
include_once WEBCLASSES . 'am/aminvclass.php';

//Amazon Class Declarations
$amclient = new \am\amclient($user_id);
$amazon = new \am\amazonclass($amclient);
$amord = new \amord\amordclass($amclient);
$aminv = new \aminv\aminvclass($amclient);