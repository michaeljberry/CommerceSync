<?php

namespace models\channels\product;


use models\ModelDB as MDB;

class ProductAvailability
{

    public static function getByProductId($productID, $storeID)
    {
        $sql = "SELECT id 
                FROM product_availability 
                WHERE product_id = :product_id 
                AND store_id = :store_id 
                AND is_available = '1'";
        $queryParams = [
            ':product_id' => $productID,
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($productID, $storeID)
    {
        $sql = "INSERT INTO product_availability (product_id, store_id, is_available) 
                VALUES (:product_id, :store_id, 1)";
        $queryParams = [
            ":product_id" => $productID,
            ":store_id" => $storeID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($productID, $storeID)
    {
        $id = ProductAvailability::getByProductId($productID, $storeID);
        if (empty($id)) {
            $id = ProductAvailability::save($productID, $storeID);
        }
        return $id;
    }
}