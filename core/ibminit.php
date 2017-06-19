<?php

include_once WEBCLASSES . 'connect/ibmdatabase.php';
include_once WEBCLASSES . 'ibmclass.php';

$ibmdata = new GeneralIBM($ibmdb);

$errors = array();