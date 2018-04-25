<?php

namespace controllers\channels\order;


use Ecommerce\Ecommerce;

class OrderController
{
    public static function createItemXmlArray($itemID, $itemDesc, $poNumber, $qty, $amount, $sku, $upc)
    {
        return [
            'Item' => [
                'ItemId' => $itemID,
                'ItemDesc' => html_entity_decode($itemDesc),
                'POLineNumber' => $poNumber,
                'UOM' => 'EACH',
                'Qty' => $qty,
                'UCValue' => Ecommerce::formatMoneyNoComma($amount),
                'UCCurrencyCode' => '',
                'RetailValue' => '',
                'RetailCurrencyCode' => '',
                'StdPackQty' => '',
                'StdContainerQty' => '',
                'SupplierItemId' => $sku,
                'BarcodeId' => $upc,
                'BarcodeType' => 'UPC',
                'ItemNote' => ''
            ]
        ];
    }
}
