<?php

namespace controllers\channels;


class ShippingController
{

    public static function determineErlanger($shipping, $address)
    {
        if (isset($address['state'])) {
            if (
                stripos($address['address2'], '1850 Airport') &&
                stripos($address['city'], 'Erlanger') &&
                stripos($address['state'], 'KY') &&
                stripos($address['zip'], '41025')
            ) {
                $shipping = 'UPIP';
            }
        }
        return $shipping;
    }

    public static function determineCode($shipping, $shipmentMethod)
    {
        if ($shipmentMethod) {
            switch (strtolower($shipmentMethod)) {
                case 'standard':
                    $shipping = 'ZSTD';
                    break;
                case 'expedited':
                    $shipping = 'ZEXP';
                    break;
                case 'secondday':
                    $shipping = 'Z2DY';
                    break;
                case '2nd day':
                    $shipping = 'Z2ND';
                    break;
                case 'nextday':
                case 'next day':
                    $shipping = 'ZNXT';
                    break;
            }
        }
        return $shipping;
    }

    public static function code($orderTotal, $address = [], $shipmentMethod = null)
    {
        $shipping = 'ZSTD';
        if ($orderTotal >= 250) {
            $shipping = 'URIP';
        }
        $shipping = ShippingController::determineErlanger($shipping, $address);
        $shipping = ShippingController::determineCode($shipping, $shipmentMethod);
        return $shipping;
    }
}