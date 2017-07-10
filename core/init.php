<?php
session_start();
error_reporting(-1);
$user_id = 838;
require 'config.php';
require WEBVENDOR . 'autoload.php';

spl_autoload_register(function ($class) {
    $file = WEBCLASSES . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

//EcomDash Classes
include_once 'classes/ecd/ecdclass.php';
include_once 'classes/ecd/ecdordclass.php';
include_once 'classes/ecd/ecdinvclass.php';

include_once 'classes/query/querybuilder.php';

include_once WEBCLASSES . 'template.php';
require WEBCORE . 'Functions.php';

$template = new Template();
$users = new User();
$general = new General();

//EcomDash Class Declarations
$ecd = new \ecd\ecdclass();
$ecdord = new \ecdord\ecdordclass();
$ecdinv = new \ecdinv\ecdinvclass();

$ecommerce = new \ecommerce\Ecommerce();

use PhpRbac\Rbac;

$rbac = new Rbac();
require WEBPLUGIN . 'ecd/ecdvar.php';
if ($general->logged_in() === true) {
    $user_id = $_SESSION['id'];
    $user = $users->userdata($user_id);
    $firstname = $user['first_name'];
    $lastname = $user['last_name'];
    $useremail = $user['email'];
    $company_id = $user['company_id'];

    //Walmart Variables
    require WEBPLUGIN . 'wm/wmvar.php';

    //EcomDash Variables
//    require WEBPLUGIN . 'ecd/ecdvar.php';
} else {

}
$errors = array();