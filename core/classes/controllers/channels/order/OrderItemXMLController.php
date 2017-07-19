<?php

namespace controllers\channels\order;


use models\channels\order\OrderItem;

class OrderItemXMLController
{

    public static function create(OrderItem $orderItem)
    {
        return [
            'Item' => [
                'ItemId' => $orderItem->getSku()->getSku(),
                'ItemDesc' => "<![CDATA[ {$orderItem->getTitle()} ]]>",
                'POLineNumber' => $orderItem->getPoNumber(),
                'UOM' => 'EACH',
                'Qty' => $orderItem->getQuantity(),
                'UCValue' => $orderItem->getPrice(),
                'UCCurrencyCode' => '',
                'RetailValue' => '',
                'RetailCurrencyCode' => '',
                'StdPackQty' => '',
                'StdContainerQty' => '',
                'SupplierItemId' => $orderItem->getSku()->getSku(),
                'BarcodeId' => $orderItem->getUpc(),
                'BarcodeType' => 'UPC',
                'ItemNote' => ''
            ]
        ];
    }

}