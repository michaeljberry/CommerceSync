<?php

namespace models\channels\order;


class OrderItemXML
{

    public static function create($sku, $title, $poNum, $qty, $principle, $upc)
    {
        $item_xml = "<Item>
            <ItemId>$sku</ItemId>
            <ItemDesc><![CDATA[ $title ]]></ItemDesc>
            <POLineNumber>$poNum</POLineNumber>
            <UOM>EACH</UOM>
            <Qty>$qty</Qty>
            <UCValue>$principle</UCValue>
            <UCCurrencyCode></UCCurrencyCode>
            <RetailValue></RetailValue>
            <RetailCurrencyCode></RetailCurrencyCode>
            <StdPackQty></StdPackQty>
            <StdContainerQty></StdContainerQty>
            <SupplierItemId>$sku</SupplierItemId>
            <BarcodeId>$upc</BarcodeId>
            <BarcodeType>UPC</BarcodeType>
            <ItemNote></ItemNote>
        </Item>";
        return $item_xml;
    }

}