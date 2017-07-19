<?php

namespace controllers\channels\order;


use models\channels\order\OrderItem;

class OrderItemXMLController
{

    public static function create(OrderItem $orderItem)
    {
        return OrderController::createItemXmlArray(
            $orderItem->getSku()->getSku(),
            $orderItem->getTitle(),
            $orderItem->getPoNumber(),
            $orderItem->getQuantity(),
            $orderItem->getPrice(),
            $orderItem->getSku()->getSku(),
            $orderItem->getUpc()
        );
    }

}