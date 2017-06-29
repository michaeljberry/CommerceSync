<?php

namespace models\channels;



use models\ModelDB as MDB;

class Stock
{

    public static function getIdFromSKUId($sku_id)
    {
        $sql = "SELECT id 
                FROM stock 
                WHERE sku_id = :sku_id";
        $query_params = [
            ':sku_id' => $sku_id
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function save($sku_id, $condition_id = null, $uofm = 1)
    {
        $sql = "INSERT INTO stock (sku_id, condition_id, uofm_id) 
                VALUES (:sku_id, :condition_id, :uofm_id)";
        $query_params = [
            ":sku_id" => $sku_id,
            ":condition_id" => $condition_id,
            ":uofm_id" => 1
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function searchOrInsert($sku_id, $condition_id = null, $uofm = 1)
    {
        $stock_id = Stock::getIdFromSKUId($sku_id);
        if (empty($stock_id)) {
            $stock_id = Stock::save($sku_id, $condition_id, $uofm);
        }
        return $stock_id;
    }
}