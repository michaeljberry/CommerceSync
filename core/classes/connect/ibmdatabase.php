<?php

$ibmdb = new PDO('odbc:DEV');
$ibmdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);