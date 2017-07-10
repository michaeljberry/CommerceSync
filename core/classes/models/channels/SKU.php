<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;
use PDO;

class SKU
{

    public static function save($sku, $productId = null)
    {
        if (!$productId) {
            $sql = "INSERT INTO sku (sku) 
                    VALUES (:sku)";
            $queryParams = [
                ':sku' => $sku
            ];
        } else {
            $sql = "INSERT INTO sku (product_id, sku) 
                    VALUES (:product_id, :sku) 
                    ON DUPLICATE KEY UPDATE product_id = :product_id2";
            $queryParams = [
                ':product_id' => $productId,
                ':sku' => $sku,
                ':product_id2' => $productId
            ];
        }

        return MDB::query($sql, $queryParams, 'id');
    }

    public static function getById($sku_id)
    {
        $sql = "SELECT sku.sku 
                FROM sku 
                WHERE id = :sku_id";
        $queryParams = [
            'sku_id' => $sku_id
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
        $sku_id = SKU::getId($sku);
        if (empty($sku_id) && !empty($sku)) {
            $sku_id = SKU::save($sku);
        }
        return $sku_id;
    }

    public static function getIdFromProductId($product_id)
    {
        $sql = "SELECT id 
                FROM sku 
                WHERE product_id = :product_id";
        $queryParams = [
            ':product_id' => $product_id
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getSKUCosts($sku, $table)
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