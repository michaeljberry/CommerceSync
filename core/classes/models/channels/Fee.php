<?php

namespace models\channels;


use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class Fee
{

    public static function getCategoryFeeOfSKU($table, $table2, $sku)
    {
        $table = CHC::sanitize_table_name($table);
        $table2 = CHC::sanitize_table_name($table2);
        $sql = "SELECT category_fee 
                FROM $table cat 
                LEFT JOIN $table2 list ON cat.category_id = list.primary_category 
                WHERE list.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function getCategory($categoryID)
    {
        $sql = "SELECT category_fee 
                FROM categories_ebay 
                WHERE category_id = :category_id";
        $query_params = [
            ':category_id' => $categoryID
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function saveCategory($categoryID, $fee)
    {
        $sql = "UPDATE categories_ebay 
                SET category_fee = :fee 
                WHERE category_id = :category_id";
        $query_params = [
            ':fee' => $fee,
            ':category_id' => $categoryID
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }
}