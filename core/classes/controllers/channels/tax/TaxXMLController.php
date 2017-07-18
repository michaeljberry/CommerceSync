<?php

namespace controllers\channels\tax;


use models\channels\order\Order;

class TaxXMLController
{

    public static function create($stateTaxItemName, Order $order)
    {
        $itemXml = "
            <Item>
                <ItemId>$stateTaxItemName</ItemId>
                <ItemDesc><![CDATA[ $stateTaxItemName ]]></ItemDesc>
                <POLineNumber>{$order->getPoNumber()}</POLineNumber>
                <UOM>EACH</UOM>
                <Qty>1</Qty>
                <UCValue>{$order->getTax()->get()}</UCValue>
                <UCCurrencyCode></UCCurrencyCode>
                <RetailValue></RetailValue>
                <RetailCurrencyCode></RetailCurrencyCode>
                <StdPackQty></StdPackQty>
                <StdContainerQty></StdContainerQty>
                <SupplierItemId>$stateTaxItemName</SupplierItemId>
                <BarcodeId></BarcodeId>
                <BarcodeType>UPC</BarcodeType>
                <ItemNote></ItemNote>
            </Item>";
        return $itemXml;
    }

    public static function getItemXml($stateTaxItemName, Order $order)
    {
        return TaxXMLController::create($stateTaxItemName, $order);
    }

}