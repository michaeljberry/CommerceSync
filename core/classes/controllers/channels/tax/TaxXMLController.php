<?php

namespace controllers\channels\tax;


use controllers\channels\order\OrderController;
use models\channels\order\Order;

class TaxXMLController
{

    public static function create($stateTaxItemName, Order $order)
    {
        return OrderController::createItemXmlArray(
            $stateTaxItemName,
            $stateTaxItemName,
            $order->getPoNumber(),
            '1',
            $order->getTax()->get(),
            $stateTaxItemName,
            ''
        );
    }

    public static function getItemXml($stateTaxItemName, Order $order)
    {
        return TaxXMLController::create($stateTaxItemName, $order);
    }

}