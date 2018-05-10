<?php
define("ROOT", __DIR__ . "/../");

if (file_exists(ROOT . '.local')) {
    $localArray = parse_ini_file(ROOT . '.local');
    define('LOCAL', true);
    define('ROOTFOLDER', $localArray['ROOT_FOLDER']);
} else {
    define('LOCAL', false);
    define('ROOTFOLDER', ROOT);
}

define('PORTALFOLDER', ROOTFOLDER);
define("RELPLUGIN", 'plugin/');
setLocale(LC_MONETARY, 'en_US.UTF-8');

if (file_exists(ROOT . '.env')) {
    //Browser-based definitions
    define("WEBROOT", ROOT);
    define('WEBINCLUDES', '/includes/');
} else {
    //Console-based definitions
    define("WEBROOT", ROOTFOLDER);
    define('WEBINCLUDES', WEBROOT . 'includes/');
}

define('WEBCORE', WEBROOT . 'core/');
define('WEBCLASSES', WEBCORE . 'classes/');
define('WEBVENDOR', WEBROOT . 'vendor/');
define('WEBPLUGIN', WEBROOT . 'plugin/');
define('WEBCSS', WEBINCLUDES . 'css/');
define('WEBJS', WEBINCLUDES . 'js/');