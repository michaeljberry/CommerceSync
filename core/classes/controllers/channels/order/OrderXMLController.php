<?php

namespace controllers\channels\order;

use controllers\channels\XMLController;
use Ecommerce\Ecommerce;
use models\channels\order\Order;

class OrderXMLController
{

    public static function create(Order $order)
    {
        return [
            'NAMM_PO~version=2007.1' => [
                'Id' => "S2S{$order->getChannelAccount()}_PO{$order->getOrderNumber()}",
                'Timestamp' => $order->getPurchaseDate(),
                'BuyerId' => $order->getChannelAccount(),
                'BuyerIdDesc' => getenv("SELLER_NAME") . " {$order->getChannelName()}",
                'PO' => $order->getOrderNumber(),
                'Backorder' => 'Y',
                'SupplierId' => '33076',
                'SupplierName' => getenv("SUPPLIER"),
                'TermsCode' => 'P999',
                'TermsDays' => '0',
                'TermsDate' => '12/31/1899',
                'TermsPercent' => Ecommerce::formatMoneyNoComma($order->getShippingPrice()),
                'TermsPercentDays' => '0',
                'ShipInstructions' => '',
                'TranspCode' => $order->getShippingCode(),
                'TranspDesc' => '',
                'TranspCarrier' => '',
                'TranspTime' => '0',
                'TranspTerms' => '',
                'DateOrdered' => $order->getPurchaseDate(),
                'DateBeginShip' => '12/31/1899',
                'DateEndShip' => '12/31/1899',
                'DateCancel' => '12/31/1899',
                'BuyerName' => '',
                'BuyerPhone' => $order->getBuyer()->getPhone(),
                'POComments' => '',
                'ShipToName' => "{$order->getBuyer()->getFirstName()} {$order->getBuyer()->getLastName()}",
                'ShipToId' => $order->getChannelAccount(),
                'ShipToAddress1' => $order->getBuyer()->getStreetAddress(),
                'ShipToAddress2' => $order->getBuyer()->getStreetAddress2(),
                'ShipToAddress3' => '',
                'ShipToAddress4' => '',
                'ShipToCity' => $order->getBuyer()->getCity()->get(),
                'ShipToState' => $order->getBuyer()->getState()->get(),
                'ShipToPostalCode' => $order->getBuyer()->getZipCode()->get(),
                'ShipToCountry' => '',
                'ShipToCountryCode' => $order->getBuyer()->getCountry(),
                'SoldToName' => getenv("SELLER_NAME") . " {$order->getChannelName()}",
                'SoldToId' => $order->getChannelAccount(),
                'SoldToAddress1' => getenv("SELLER_ADDRESS"),
                'SoldToAddress2' => '',
                'SoldToAddress3' => '',
                'SoldToAddress4' => '',
                'SoldToCity' => getenv("SELLER_CITY"),
                'SoldToState' => getenv("SELLER_STATE"),
                'SoldToPostalCode' => getenv("SELLER_ZIPCODE"),
                'SoldToCountry' => '',
                'SoldToCountryCode' => '',
                'PORevisionNumber' => '',
                'POStatusIndicator' => '',
                'ASNRequirement' => '',
                'POFileType' => '',
            ]
        ];
    }

    public static function compile(Order $order) {
        $orderXML = OrderXMLController::createOrder($order);
        $orderItems = OrderXMLController::createItems($order);
        $tax = OrderXMLController::createTax($order);
        if(!empty($tax)) {
            $orderItems .= $tax;
        }
        $xml = strstr($orderXML, '</NAMM_PO>', true);
        $xml .= $orderItems;
        $xml .= '</NAMM_PO>';
        Ecommerce::dd($xml);
        return $xml;
    }

    protected static function createOrder(Order $order): string
    {
        return XMLController::makeXML(OrderXMLController::create($order));
    }

    public static function createItems(Order $order): string
    {
        $xml = '';
        foreach ($order->getOrderItems() as $item) {
            $xml .= XMLController::makeXML($item->getItemXml());
        }
        return $xml;
    }

    public static function createTax(Order $order)
    {
        $order->getTax()->settleTax($order);
        return XMLController::makeXML($order->getTax()->getTaxXml());
    }

}