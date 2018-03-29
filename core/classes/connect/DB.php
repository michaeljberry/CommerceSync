<?php

namespace connect;

use PDO;

class DB
{
    protected static $instance = null;

//    protected function __construct() {}
//    protected function __clone() {}

    public static function instance()
    {
        if (self::$instance === null) {
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => TRUE,
            );
            $dsn = 'mysql:host=' . getenv("DB_HOST") . ';port=' . getenv("DB_PORT") . ';dbname=' . getenv("DB_NAME");
            self::$instance = new PDO($dsn, getenv("DB_USER"), getenv("DB_PASS"), $opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public static function run($sql, $args = NULL)
    {
        $stmt = self::prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
