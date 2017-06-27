<?php

namespace ecommerce;

interface EcommerceInterface
{

    static function getStoreID();

    static function instance($userID);

    static function __callStatic($name, $arguments);

}
