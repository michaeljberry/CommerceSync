<?php

namespace models\channels;


use models\ModelDB as MDB;
use PDO;

class Tax
{

    public static function getItemXml($state_code, $poNumber, $totalTax, $stateTaxItemName = '')
    {
        $itemXml = '';
        if (!empty($stateTaxItemName)) {
            $itemXml .= TaxXML::create($poNumber, $totalTax, '', $stateTaxItemName);
        } else {
            if (strtolower($state_code) == 'id' || strtolower($state_code) == 'idaho') {
                $itemXml .= TaxXML::create($poNumber, number_format($totalTax, 2), 'ID');
            } elseif (strtolower($state_code) == 'ca' || strtolower($state_code) == 'california') {
                $itemXml .= TaxXML::create($poNumber, number_format($totalTax, 2), 'CA');
            } elseif (strtolower($state_code) == 'wa' || strtolower($state_code) == 'washington') {
                $itemXml .= TaxXML::create($poNumber, number_format($totalTax, 2), 'WA');
            }
        }
        return $itemXml;
    }

    public static function getCompanyInfo($companyID)
    {
        $sql = "SELECT s.abbr, t.tax_rate, t.tax_line_name, t.shipping_taxed 
                FROM taxes t 
                INNER JOIN state s ON s.id = t.state_id 
                WHERE company_id = :company_id";
        $queryParams = [
            ':company_id' => $companyID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll', PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public static function state($stateArray, $state)
    {
        $taxable = false;
        foreach ($stateArray as $s => $value) {
            if ($s == $state) {
                $taxable = true;
            }
        }
        return $taxable;
    }

    public static function calculate($stateTaxArray, $totalWithoutTax, $totalShipping)
    {
        $taxRate = $stateTaxArray['tax_rate'] / 100;
        $totalTax = number_format($totalWithoutTax * $taxRate, 2);
        if ($stateTaxArray['shipping_taxed']) {
            $totalTax += number_format($totalShipping * $taxRate, 2);
        }
        return $totalTax;
    }
}