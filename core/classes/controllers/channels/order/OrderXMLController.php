<?php

namespace controllers\channels\order;

use ecommerce\Ecommerce;
use models\channels\order\Order;

class OrderXMLController
{

    public static function create(Order $order) {
        $sellerName = SELLER_NAME;
        $sellerAddress = SELLER_ADDRESS;
        $sellerCity = SELLER_CITY;
        $sellerState = SELLER_STATE;
        $sellerZipCode = SELLER_ZIPCODE;
        $supplier = SUPPLIER;
        $xml = <<<EOD
        <NAMM_PO version="2007.1">
            <Id>S2S{$order->getChannelAccount()}_PO{$order->getOrderNum()}</Id>
            <Timestamp>{$order->getPurchaseDate()}</Timestamp>
            <BuyerId>{$order->getChannelAccount()}</BuyerId>
            <BuyerIdDesc>{$sellerName} {$order->getChannelName()}</BuyerIdDesc>
            <PO>{$order->getOrderNum()}</PO>
            <Backorder>Y</Backorder>
            <SupplierId>33076</SupplierId>
            <SupplierName>{$supplier}</SupplierName>
            <TermsCode>P999</TermsCode>
            <TermsDays>0</TermsDays>
            <TermsDate>12/31/1899</TermsDate>
            <TermsPercent>{$order->getShippingPrice()}</TermsPercent>
            <TermsPercentDays>0</TermsPercentDays>
            <ShipInstructions></ShipInstructions>
            <TranspCode>{$order->getShippingCode()}</TranspCode>
            <TranspDesc></TranspDesc>
            <TranspCarrier></TranspCarrier>
            <TranspTime>0</TranspTime>
            <TranspTerms></TranspTerms>
            <DateOrdered>{$order->getPurchaseDate()}</DateOrdered>
            <DateBeginShip>12/31/1899</DateBeginShip>
            <DateEndShip>12/31/1899</DateEndShip>
            <DateCancel>12/31/1899</DateCancel>
            <BuyerName></BuyerName>
            <BuyerPhone>{$order->getBuyer()->getPhone()}</BuyerPhone>
            <POComments></POComments>
            <ShipToName>{$order->getBuyer()->getFirstName()} {$order->getBuyer()->getLastName()}</ShipToName>
            <ShipToId>{$order->getChannelAccount()}</ShipToId>
            <ShipToAddress1>{$order->getBuyer()->getStreetAddress()}</ShipToAddress1>
            <ShipToAddress2>{$order->getBuyer()->getStreetAddress2()}</ShipToAddress2>
            <ShipToAddress3></ShipToAddress3>
            <ShipToAddress4></ShipToAddress4>
            <ShipToCity>{$order->getBuyer()->getCity()->get()}</ShipToCity>
            <ShipToState>{$order->getBuyer()->getState()->get()}</ShipToState>
            <ShipToPostalCode>{$order->getBuyer()->getZipCode()->get()}</ShipToPostalCode>
            <ShipToCountry></ShipToCountry>
            <ShipToCountryCode>{$order->getBuyer()->getCountry()}</ShipToCountryCode>
            <SoldToName>{$sellerName} {$order->getChannelName()}</SoldToName>
            <SoldToId>{$order->getChannelAccount()}</SoldToId>
            <SoldToAddress1>{$sellerAddress}</SoldToAddress1>
            <SoldToAddress2></SoldToAddress2>
            <SoldToAddress3></SoldToAddress3>
            <SoldToAddress4></SoldToAddress4>
            <SoldToCity>{$sellerCity}</SoldToCity>
            <SoldToState>{$sellerState}</SoldToState>
            <SoldToPostalCode>{$sellerZipCode}</SoldToPostalCode>
            <SoldToCountry></SoldToCountry>
            <SoldToCountryCode></SoldToCountryCode>
            <PORevisionNumber></PORevisionNumber>
            <POStatusIndicator></POStatusIndicator>
            <ASNRequirement></ASNRequirement>
            <POFileType></POFileType>
EOD;
        $xml .= OrderXMLController::createItems($order);
        $xml .= OrderXMLController::createTax($order);
        $xml .= "</NAMM_PO>";
        Ecommerce::dd($xml);
        return $xml;
    }

    public static function createItems(Order $order): string
    {
        $xml = "";
        foreach ($order->getOrderItems() as $item) {
            $xml .= $item->getItemXml();
        }
        return $xml;
    }

    public static function createTax(Order $order)
    {
        $xml = "";
        $order->getTax()->settleTax($order);
        $xml .= $order->getTax()->getTaxXml();
        return $xml;
    }

}