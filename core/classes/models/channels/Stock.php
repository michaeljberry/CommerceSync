<?php

namespace models\channels;

use models\ModelDB as MDB;

class Stock
{

    public static function getIdBySkuId($skuID)
    {
        $sql = "SELECT id 
                FROM stock 
                WHERE sku_id = :sku_id";
        $queryParams = [
            ':sku_id' => $skuID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($skuID, $conditionID = null, $uofmID = 1)
    {
        $sql = "INSERT INTO stock (sku_id, condition_id, uofm_id) 
                VALUES (:sku_id, :condition_id, :uofm_id)";
        $queryParams = [
            ":sku_id" => $skuID,
            ":condition_id" => $conditionID,
            ":uofm_id" => $uofmID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($skuID, $conditionID = null, $uofm = 1)
    {
        $stock_id = Stock::getIdBySkuId($skuID);
        if (empty($stock_id)) {
            $stock_id = Stock::save($skuID, $conditionID, $uofm);
        }
        return $stock_id;
    }
}