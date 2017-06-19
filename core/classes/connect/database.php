<?php

namespace models;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
    function __construct()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => DB_CHAR,
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''

        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}