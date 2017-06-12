<?php

include_once 'connect/ibmdatabase.php';
include_once 'classes/ibmclass.php';

$ibmdata = new GeneralIBM($ibmdb);

$errors = array();