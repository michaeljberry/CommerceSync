<?php

namespace models\channels\order;


class OrderXML
{

    public static function create(
        $channelNumber,
        $channel,
        $orderNum,
        $timestamp,
        $shippingAmount,
        $shipping,
        $buyerPhone,
        $shipToName,
        $address,
        $address2,
        $city,
        $state,
        $zip,
        $country,
        $itemXML
    ) {
        $xml = <<<EOD
        <NAMM_PO version="2007.1">
            <Id>S2S{$channelNumber}_PO{$orderNum}</Id>
            <Timestamp>$timestamp</Timestamp>
            <BuyerId>$channelNumber</BuyerId>
            <BuyerIdDesc>My Music Life $channel</BuyerIdDesc>
            <PO>$orderNum</PO>
            <Backorder>Y</Backorder>
            <SupplierId>33076</SupplierId>
            <SupplierName>Chesbro Music Co.</SupplierName>
            <TermsCode>P999</TermsCode>
            <TermsDays>0</TermsDays>
            <TermsDate>12/31/1899</TermsDate>
            <TermsPercent>$shippingAmount</TermsPercent>
            <TermsPercentDays>0</TermsPercentDays>
            <ShipInstructions></ShipInstructions>
            <TranspCode>$shipping</TranspCode>
            <TranspDesc></TranspDesc>
            <TranspCarrier></TranspCarrier>
            <TranspTime>0</TranspTime>
            <TranspTerms></TranspTerms>
            <DateOrdered>$timestamp</DateOrdered>
            <DateBeginShip>12/31/1899</DateBeginShip>
            <DateEndShip>12/31/1899</DateEndShip>
            <DateCancel>12/31/1899</DateCancel>
            <BuyerName></BuyerName>
            <BuyerPhone>$buyerPhone</BuyerPhone>
            <POComments></POComments>
            <ShipToName>$shipToName</ShipToName>
            <ShipToId>$channelNumber</ShipToId>
            <ShipToAddress1>$address</ShipToAddress1>
            <ShipToAddress2>$address2</ShipToAddress2>
            <ShipToAddress3></ShipToAddress3>
            <ShipToAddress4></ShipToAddress4>
            <ShipToCity>$city</ShipToCity>
            <ShipToState>$state</ShipToState>
            <ShipToPostalCode>$zip</ShipToPostalCode>
            <ShipToCountry></ShipToCountry>
            <ShipToCountryCode>$country</ShipToCountryCode>
            <SoldToName>My Music Life $channel</SoldToName>
            <SoldToId>$channelNumber</SoldToId>
            <SoldToAddress1>PO Box 2009</SoldToAddress1>
            <SoldToAddress2></SoldToAddress2>
            <SoldToAddress3></SoldToAddress3>
            <SoldToAddress4></SoldToAddress4>
            <SoldToCity>Idaho Falls</SoldToCity>
            <SoldToState>ID</SoldToState>
            <SoldToPostalCode>83403-2009</SoldToPostalCode>
            <SoldToCountry></SoldToCountry>
            <SoldToCountryCode></SoldToCountryCode>
            <PORevisionNumber></PORevisionNumber>
            <POStatusIndicator></POStatusIndicator>
            <ASNRequirement></ASNRequirement>
            <POFileType></POFileType>
            $itemXML
            </NAMM_PO>
EOD;
        return $xml;
    }
}