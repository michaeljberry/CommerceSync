<?php

namespace models\channels;


use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class Fee
{

    public static function getCategoryBySku($table, $table2, $sku)
    {
        $table = CHC::sanitize_table_name($table);
        $table2 = CHC::sanitize_table_name($table2);
        $sql = "SELECT category_fee 
                FROM $table cat 
                LEFT JOIN $table2 list ON cat.category_id = list.primary_category 
                WHERE list.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getCategoryById($categoryID)
    {
        $sql = "SELECT category_fee 
                FROM categories_ebay 
                WHERE category_id = :category_id";
        $queryParams = [
            ':category_id' => $categoryID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function updateCategory($categoryID, $fee)
    {
        $sql = "UPDATE categories_ebay 
                SET category_fee = :fee 
                WHERE category_id = :category_id";
        $queryParams = [
            ':fee' => $fee,
            ':category_id' => $categoryID
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }
}