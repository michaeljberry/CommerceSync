<?php

namespace models\channels;


use models\ModelDB as MDB;

class Product
{

    public static function save($name, $sub_title, $description, $upc, $weight)
    {
        $sql = "INSERT INTO product (product.name, subtitle, description, upc, weight) 
                VALUES (:name, :subtitle, :description, :upc, :weight)";
        $queryParams = [
            ':name' => $name,
            ':subtitle' => $sub_title,
            ':description' => $description,
            ':upc' => $upc,
            ':weight' => $weight
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function getFromSKU($sku)
    {
        $sql = "SELECT product.id, product.upc, product.status 
                FROM product 
                JOIN sku ON sku.product_id = product.id 
                WHERE sku.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function getId($sku)
    {
        $sql = "SELECT product.id 
                FROM product 
                JOIN sku ON sku.product_id = product.id 
                WHERE sku.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function updateUPC($productId, $upc)
    {
        $sql = "UPDATE product 
                SET upc = :upc 
                WHERE id = :id";
        $queryParams = [
            ':upc' => $upc,
            ':id' => $productId
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateStatus($productId, $status)
    {
        $sql = "UPDATE product 
                SET status = :status 
                WHERE id = :id";
        $queryParams = [
            ':status' => $status,
            ':id' => $productId
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsertFromSKUGetSKUId($sku, $name, $subTitle, $description, $upc, $weight, $status = '')
    {
        $results = Product::getFromSKU($sku);
        $productID = $results['id'];
        $upc2 = $results['upc'];
        $active = $results['status'];
        if (empty($productID)) {
            $productID = Product::save($name, $subTitle, $description, $upc, $weight);
            $skuID = SKU::save($sku, $productID);
        } elseif (empty($upc2)) {
            $productID = Product::updateUPC($productID, $upc);
            $skuID = SKU::getIdFromProductId($productID);
        } elseif (empty($active)) {
            $productID = Product::updateStatus($productID, $status);
            $skuID = SKU::getIdFromProductId($productID);
        } else {
            $skuID = SKU::getId($sku);
        }
        return $skuID;
    }

    public static function searchOrInsertFromSKUGetId($sku, $name, $subTitle, $description, $upc, $weight)
    {
        $productID = Product::getId($sku);
        if (!empty($productID)) {
            return $productID;
        }
        $productID = Product::save($name, $subTitle, $description, $upc, $weight);
        SKU::save($sku, $productID);
    }

    public static function getAvailability($productId, $storeID)
    {
        $sql = "SELECT id 
                FROM product_availability 
                WHERE product_id = :product_id 
                AND store_id = :store_id 
                AND is_available = '1'";
        $queryParams = [
            ':product_id' => $productId,
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function saveAvailability($productID, $storeID)
    {
        $sql = "INSERT INTO product_availability (product_id, store_id, is_available) 
                VALUES (:product_id, :store_id, 1)";
        $queryParams = [
            ":product_id" => $productID,
            ":store_id" => $storeID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsertAvailability($productID, $storeID)
    {
        $id = Product::getAvailability($productID, $storeID);
        if (empty($id)) {
            $id = Product::saveAvailability($productID, $storeID);
        }
        return $id;
    }

    public static function getAllInfo($sku)
    {
        $sql = "SELECT * 
                FROM product p 
                JOIN sku sk ON p.id = sk.product_id 
                JOIN stock st ON st.sku_id = sk.id 
                WHERE sk.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

}