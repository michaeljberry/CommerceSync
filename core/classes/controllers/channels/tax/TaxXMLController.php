<?php

namespace controllers\channels\tax;


use models\channels\order\Order;

class TaxXMLController
{

    public static function create($stateTaxItemName, Order $order)
    {
        return [
            'Item' => [
                'ItemId' => $stateTaxItemName,
                'ItemDesc' => "<![CDATA[ $stateTaxItemName ]]>",
                'POLineNumber' => $order->getPoNumber(),
                'UOM' => 'EACH',
                'Qty' => '1',
                'UCValue' => $order->getTax()->get(),
                'UCCurrencyCode' => '',
                'RetailValue' => '',
                'RetailCurrencyCode' => '',
                'StdPackQty' => '',
                'StdContainerQty' => '',
                'SupplierItemId' => $stateTaxItemName,
                'BarcodeId' => '',
                'BarcodeType' => 'UPC',
                'ItemNote' => ''
            ]
        ];
    }

    public static function getItemXml($stateTaxItemName, Order $order)
    {
        return TaxXMLController::create($stateTaxItemName, $order);
    }

}