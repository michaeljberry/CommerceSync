<?php

namespace controllers\channels\order;


use models\channels\order\OrderItem;

class OrderItemXMLController
{

    public static function create(OrderItem $orderItem)
    {
        return "<Item>
            <ItemId>{$orderItem->getSku()}</ItemId>
            <ItemDesc><![CDATA[ {$orderItem->getTitle()} ]]></ItemDesc>
            <POLineNumber>{$orderItem->getPoNumber()}</POLineNumber>
            <UOM>EACH</UOM>
            <Qty>{$orderItem->getQuantity()}</Qty>
            <UCValue>{$orderItem->getPrice()}</UCValue>
            <UCCurrencyCode></UCCurrencyCode>
            <RetailValue></RetailValue>
            <RetailCurrencyCode></RetailCurrencyCode>
            <StdPackQty></StdPackQty>
            <StdContainerQty></StdContainerQty>
            <SupplierItemId>{$orderItem->getSku()}</SupplierItemId>
            <BarcodeId>{$orderItem->getUpc()}</BarcodeId>
            <BarcodeType>UPC</BarcodeType>
            <ItemNote></ItemNote>
        </Item>";
    }

}