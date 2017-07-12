<?php

namespace models\channels\product;

use models\channels\SKU;
use models\ModelDB as MDB;

class Product
{

    public static function save($name, $subTitle, $description, $upc, $weight)
    {
        $sql = "INSERT INTO product (product.name, subtitle, description, upc, weight) 
                VALUES (:name, :subtitle, :description, :upc, :weight)";
        $queryParams = [
            ':name' => $name,
            ':subtitle' => $subTitle,
            ':description' => $description,
            ':upc' => $upc,
            ':weight' => $weight
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function getBySku($sku)
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

    public static function getIdBySku($sku)
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

    public static function updateUpc($id, $upc)
    {
        $sql = "UPDATE product 
                SET upc = :upc 
                WHERE id = :id";
        $queryParams = [
            ':upc' => $upc,
            ':id' => $id
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateStatus($id, $status)
    {
        $sql = "UPDATE product 
                SET status = :status 
                WHERE id = :id";
        $queryParams = [
            ':status' => $status,
            ':id' => $id
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsertBySku($sku, $name, $subTitle, $description, $upc, $weight, $status = '')
    {
        $results = Product::getBySku($sku);
        $productID = $results['id'];
        $upc2 = $results['upc'];
        $active = $results['status'];
        if (empty($productID)) {
            $productID = Product::save($name, $subTitle, $description, $upc, $weight);
            $skuID = SKU::save($sku, $productID);
        } elseif (empty($upc2)) {
            $productID = Product::updateUpc($productID, $upc);
            $skuID = SKU::getIdByProductId($productID);
        } elseif (empty($active)) {
            $productID = Product::updateStatus($productID, $status);
            $skuID = SKU::getIdByProductId($productID);
        } else {
            $skuID = SKU::getId($sku);
        }
        return $skuID;
    }

    public static function searchOrInsert($sku, $name, $subTitle, $description, $upc, $weight)
    {
        $id = Product::getIdBySku($sku);
        if (!empty($id)) {
            return $id;
        }
        $id = Product::save($name, $subTitle, $description, $upc, $weight);
        SKU::save($sku, $id);
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