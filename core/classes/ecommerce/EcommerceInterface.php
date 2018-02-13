<?php

namespace ecommerce;

interface EcommerceInterface
{

    static function getStoreId();

    static function instance();

    static function __callStatic($name, $arguments);

}
