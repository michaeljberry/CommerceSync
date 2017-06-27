<?php

namespace connect;

use PDO;

class IBMDB
{
    protected static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            if (file_exists(ROOT . '.local')) {
                self::$instance = new PDO('odbc:DRIVER={iSeries Access ODBC Driver};SYSTEM=' . IBM_HOST . ';DATABASE=' . IBM_NAME . ';UID=' . IBM_USER . ';PWD=' . IBM_PASS . ';NAMING=1');
            } else {
                self::$instance = new PDO('odbc:DEV');
            }
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

