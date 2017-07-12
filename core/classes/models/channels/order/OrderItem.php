<?php

namespace models\channels\order;

use models\ModelDB as MDB;

class OrderItem
{
    public static function getByOrderId($orderID)
    {
        $sql = "SELECT s.sku, p.name, oi.price, oi.quantity 
                FROM order_item oi 
                JOIN sku s ON oi.sku_id = s.id 
                JOIN product p ON s.product_id = p.id 
                WHERE order_id = :order_id";
        $queryParams = [
            ":order_id" => $orderID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function updateQty($orderNum, $sku, $qty)
    {
        $sql = "UPDATE order_item oi 
                JOIN sync.order o ON o.id = oi.order_id 
                JOIN sku sk ON sk.id = oi.sku_id 
                SET oi.quantity = :quantity 
                WHERE o.order_num = :order_num 
                AND sk.sku = :sku";
        $queryParams = [
            ':quantity' => $qty,
            ':order_num' => $orderNum,
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function save($orderID, $skuID, $price, $qty, $itemID = '')
    {
        $sql = "INSERT INTO order_item (order_id, sku_id, price, item_id, quantity) 
                VALUES (:order_id, :sku_id, :price, :item_id, :quantity)";
        $queryParams = [
            ':order_id' => $orderID,
            ':sku_id' => $skuID,
            ':price' => $price,
            ':item_id' => $itemID,
            ':quantity' => $qty
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }
}