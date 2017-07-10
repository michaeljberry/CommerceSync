<?php

namespace models\channels;

use models\ModelDB as MDB;

class OrderItem
{
    public static function getByOrderId($id)
    {
        $sql = "SELECT s.sku, p.name, oi.price, oi.quantity 
                FROM order_item oi 
                JOIN sku s ON oi.sku_id = s.id 
                JOIN product p ON s.product_id = p.id 
                WHERE order_id = :order_id";
        $queryParams = [
            ":order_id" => $id
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function updateQty($order, $sku, $quantity)
    {
        $sql = "UPDATE order_item oi 
                JOIN sync.order o ON o.id = oi.order_id 
                JOIN sku sk ON sk.id = oi.sku_id 
                SET oi.quantity = :quantity 
                WHERE o.order_num = :order_num 
                AND sk.sku = :sku";
        $queryParams = [
            ':quantity' => $quantity,
            ':order_num' => $order,
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function save($orderID, $skuID, $price, $quantity, $itemID = '')
    {
        $sql = "INSERT INTO order_item (order_id, sku_id, price, item_id, quantity) 
                VALUES (:order_id, :sku_id, :price, :item_id, :quantity)";
        $queryParams = [
            ':order_id' => $orderID,
            ':sku_id' => $skuID,
            ':price' => $price,
            ':item_id' => $itemID,
            ':quantity' => $quantity
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }
}