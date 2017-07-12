<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;
use PDO;

class SKU
{

    public static function create($sku)
    {
        $sql = "INSERT INTO sku (sku) 
                    VALUES (:sku)";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function update($sku, $productID)
    {
        $sql = "INSERT INTO sku (product_id, sku) 
                    VALUES (:product_id, :sku) 
                    ON DUPLICATE KEY UPDATE product_id = :product_id2";
        $queryParams = [
            ':product_id' => $productID,
            ':sku' => $sku,
            ':product_id2' => $productID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function save($sku, $productID = null)
    {
        if (!$productID) {
            return SKU::create($sku);
        }
        return SKU::update($sku, $productID);
    }

    public static function getById($id)
    {
        $sql = "SELECT sku.sku 
                FROM sku 
                WHERE id = :sku_id";
        $queryParams = [
            'sku_id' => $id
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getId($sku)
    {
        $sql = "SELECT id 
                FROM sku 
                WHERE sku.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function searchOrInsert($sku)
    {
        $id = SKU::getId($sku);
        if (empty($id) && !empty($sku)) {
            return SKU::save($sku);
        }
        return $id;
    }

    public static function getIdByProductId($productID)
    {
        $sql = "SELECT id 
                FROM sku 
                WHERE product_id = :product_id";
        $queryParams = [
            ':product_id' => $productID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getCosts($sku, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT (pc.msrp/100) as msrp, (pc.pl10/100) as pl10, (pc.pl1/100) as pl1, (pc.cost/100) as cost, lt.override_price, lt.title, p.upc 
                FROM product_cost pc 
                JOIN sku sk ON sk.id = pc.sku_id 
                JOIN product p ON p.id = sk.product_id 
                JOIN $table lt ON lt.sku = sk.sku 
                WHERE sk.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetch', PDO::FETCH_ASSOC);
    }


}