<?php

namespace models\channels;


use models\ModelDB as MDB;

class Product
{

    public static function save($name, $sub_title, $description, $upc, $weight)
    {
        $sql = "INSERT INTO product (product.name, subtitle, description, upc, weight) 
                VALUES (:name, :subtitle, :description, :upc, :weight)";
        $query_params = [
            ':name' => $name,
            ':subtitle' => $sub_title,
            ':description' => $description,
            ':upc' => $upc,
            ':weight' => $weight
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function getFromSKU($sku)
    {
        $sql = "SELECT product.id, product.upc, product.status 
                FROM product 
                JOIN sku ON sku.product_id = product.id 
                WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    public static function updateUPC($productId, $upc)
    {
        $sql = "UPDATE product SET upc = :upc WHERE id = :id";
        $query_params = [
            ':upc' => $upc,
            ':id' => $productId
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function updateStatus($productId, $status)
    {
        $sql = "UPDATE product SET status = :status WHERE id = :id";
        $query_params = [
            ':status' => $status,
            ':id' => $productId
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function searchOrInsertFromSKU($sku, $name, $subTitle, $description, $upc, $weight, $status = '')
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
}