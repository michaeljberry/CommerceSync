<?php

namespace models\channels\address;


class Address
{
    public static function countryCode($country)
    {
        return ($country == 'United States' || $country == 'US') ? 'USA' : $country;
    }
}