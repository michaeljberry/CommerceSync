<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT'] . '/');

if (file_exists(ROOT . '.local')) {
    $localArray = parse_ini_file(ROOT . '.local');
    define('LOCAL', true);
    define('ROOTFOLDER', $localArray['ROOT_FOLDER']);
} else {
    define('LOCAL', false);
    define('ROOTFOLDER', '/var/www/portal/');
}

define('PORTALFOLDER', ROOTFOLDER);
define("RELPLUGIN", 'plugin/');
setLocale(LC_MONETARY, 'en_US.UTF-8');

if (file_exists(ROOT . '.env')) {
    //Browser-based definitions
    define("WEBROOT", $_SERVER['DOCUMENT_ROOT'] . '/');
    define('WEBINCLUDES', '/includes/');
} else {
    //Console-based definitions
    define("WEBROOT", ROOTFOLDER);
    define('WEBINCLUDES', WEBROOT . 'includes/');
}

$iniArray = parse_ini_file(WEBROOT . '.env');
define('CRYPTKEY', $iniArray['CRYPT_KEY']);

define('EMAIL_USER', $iniArray['EMAIL_USER']);
define('EMAIL_USERNAME', $iniArray['EMAIL_USERNAME']);
define('EMAIL_PASSWORD', $iniArray['EMAIL_PASSWORD']);
define('EMAIL_HOST', $iniArray['EMAIL_HOST']);
define('EMAIL_PORT', $iniArray['EMAIL_PORT']);

define('WEBCORE', WEBROOT . 'core/');
define('WEBCLASSES', WEBCORE . 'classes/');
define('WEBVENDOR', WEBROOT . 'vendor/');
define('WEBPLUGIN', WEBROOT . 'plugin/');
define('WEBCSS', WEBINCLUDES . 'css/');
define('WEBJS', WEBINCLUDES . 'js/');

define('DB_HOST', $iniArray['DB_HOST']);
define('DB_PORT', $iniArray['DB_PORT']);
define('DB_NAME', $iniArray['DB_NAME']);
define('DB_USER', $iniArray['DB_USER']);
define('DB_PASS', $iniArray['DB_PASS']);
define('DB_CHAR', $iniArray['DB_CHAR']);

define('IBM_HOST', $iniArray['IBM_HOST']);
define('IBM_NAME', $iniArray['IBM_NAME']);
define('IBM_USER', $iniArray['IBM_USER']);
define('IBM_PASS', $iniArray['IBM_PASS']);

define('WOO_STORE', $iniArray['WOO_STORE']);
define('SUPPLIER', $iniArray['SUPPLIER']);
define('SELLER_NAME', $iniArray['SELLER_NAME']);
define('SELLER_ADDRESS', $iniArray['SELLER_ADDRESS']);
define('SELLER_CITY', $iniArray['SELLER_CITY']);
define('SELLER_STATE', $iniArray['SELLER_STATE']);
define('SELLER_ZIPCODE', $iniArray['SELLER_ZIPCODE']);

define('FTP_FOLDER', $iniArray['FTP_FOLDER']);
define('APP_URL', $iniArray['APP_URL']);
define('APP_NAME', $iniArray['APP_NAME']);
define('AMAZON_MERCHANTID', $iniArray['AMAZON_MERCHANTID']);
define('AMAZON_MARKETPLACEID', $iniArray['AMAZON_MARKETPLACEID']);
define('AMAZON_AWSACCESSKEY', $iniArray['AMAZON_AWSACCESSKEY']);
define('AMAZON_SECRETKEY', $iniArray['AMAZON_SECRETKEY']);