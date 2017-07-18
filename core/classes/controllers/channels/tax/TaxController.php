<?php

namespace controllers\channels\tax;

use ecommerce\Ecommerce;

class TaxController
{

    public static function stateIsTaxable($stateArray, $state)
    {
        if(array_key_exists($state, $stateArray)){
            return true;
        }
        return false;
    }

    public static function calculate($stateTaxArray, $totalWithoutTax, $totalShipping)
    {
        $taxRate = $stateTaxArray['tax_rate'] / 100;
        $totalTax = Ecommerce::formatMoney($totalWithoutTax * $taxRate);
        if ($stateTaxArray['shipping_taxed']) {
            $totalTax += Ecommerce::formatMoney($totalShipping * $taxRate);
        }
        return $totalTax;
    }
}