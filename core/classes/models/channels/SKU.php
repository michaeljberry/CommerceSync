<?php

namespace models\channels;

use models\ModelDB as MDB;

class SKU
{

    public static function skuSoi($sku)
    {
        $sql = "SELECT id FROM sku WHERE sku.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        $sku_id = MDB::query($sql, $query_params, 'fetchColumn');
        if (empty($sku_id) && !empty($sku)) {
            $sql = "INSERT INTO sku (sku) VALUES (:sku)";
            $query_params = [
                ':sku' => $sku
            ];
            $sku_id = MDB::query($sql, $query_params, 'id');
        }
        return $sku_id;
    }

    public static function getSkuIdFromProductId($product_id)
    {
        $sql = "SELECT id FROM sku WHERE product_id = :product_id";
        $query_params = [
            ':product_id' => $product_id
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }
}