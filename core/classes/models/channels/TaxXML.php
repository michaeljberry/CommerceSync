<?php

namespace models\channels;


class TaxXML
{

    public static function create($poNumber, $totalTax, $state, $stateTaxItemName = '')
    {
        $itemName = '';
        if (!empty($stateTaxItemName)) {
            $itemName = $stateTaxItemName;
        } else {
            if ($state == 'ID') {
                $itemName = "SALES TAX IDAHO @ 6%";
            } elseif ($state == 'CA') {
                $itemName = "SALES TAX CALIFORNIA";
            } elseif ($state == 'WA') {
                $itemName = "SALES TAX WASHINGTON";
            }
        }
        $itemXml = "<Item>
                    <ItemId>$itemName</ItemId>
                    <ItemDesc><![CDATA[ $itemName ]]></ItemDesc>
                    <POLineNumber>$poNumber</POLineNumber>
                    <UOM>EACH</UOM>
                    <Qty>1</Qty>
                    <UCValue>$totalTax</UCValue>
                    <UCCurrencyCode></UCCurrencyCode>
                    <RetailValue></RetailValue>
                    <RetailCurrencyCode></RetailCurrencyCode>
                    <StdPackQty></StdPackQty>
                    <StdContainerQty></StdContainerQty>
                    <SupplierItemId>$itemName</SupplierItemId>
                    <BarcodeId></BarcodeId>
                    <BarcodeType>UPC</BarcodeType>
                    <ItemNote></ItemNote>
                </Item>";
        return $itemXml;
    }
}