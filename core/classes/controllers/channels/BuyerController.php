<?php

namespace controllers\channels;


class BuyerController
{

    /**
     * @param $shipToName
     * @return array
     */
    public static function splitName($shipToName): array
    {
        $buyerName = explode(' ', $shipToName);
        $lastName = standardCase(array_pop($buyerName));
        $firstName = standardCase(implode(' ', $buyerName));
        return array($lastName, $firstName);
    }
}