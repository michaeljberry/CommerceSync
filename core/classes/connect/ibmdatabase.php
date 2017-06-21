<?php
if(file_exists(ROOT . '.local')){
    $ibmdb = new PDO('odbc:DRIVER={iSeries Access ODBC Driver};SYSTEM=' . IBM_HOST . ';DATABASE=' . IBM_NAME . ';UID=' . IBM_USER . ';PWD=' . IBM_PASS . ';NAMING=1');
}else {
    $ibmdb = new PDO('odbc:DEV');
}
$ibmdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);