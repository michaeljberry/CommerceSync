<?php

namespace models\channels;

use models\ModelDB as MDB;

class OrderItemModel
{
    public static function getOrderItemsByOrderId($id)
    {
        $sql = "SELECT s.sku, p.name, oi.price, oi.quantity FROM order_item oi JOIN sku s ON oi.sku_id = s.id JOIN product p ON s.product_id = p.id WHERE order_id = :order_id";
        $query_params = [
            ":order_id" => $id
        ];
        return MDB::query($sql, $query_params, 'fetchAll');
    }
}