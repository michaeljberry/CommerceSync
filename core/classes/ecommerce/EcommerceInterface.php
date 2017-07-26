<?php

namespace ecommerce;

interface EcommerceInterface
{

    static function getStoreId();

    static function instance($userID);

    static function __callStatic($name, $arguments);

}
