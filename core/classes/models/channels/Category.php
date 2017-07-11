<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class Category
{

    public static function get()
    {
        $sql = "SELECT cm.id as id, cm.categories_ebay_id, cm.categories_amazon_id, cm.categories_bigcommerce_id, ca.category_name AS am_cat_name, ce.category_name AS eb_cat_name, cb.category_name AS bc_cat_name 
                FROM categories_mapped cm 
                LEFT JOIN categories_amazon ca ON cm.categories_amazon_id = ca.category_id 
                LEFT JOIN categories_ebay ce ON cm.categories_ebay_id = ce.category_id 
                LEFT JOIN categories_bigcommerce cb ON cm.categories_bigcommerce_id = cb.category_id 
                ORDER BY categories_ebay_id";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getParents($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM $table 
                WHERE category_id = parent_category_id 
                ORDER BY parent_category_id ASC";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getChildren($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM $table 
                WHERE category_id != parent_category_id 
                ORDER BY parent_category_id ASC";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getInfo($catID, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM categories_$table 
                WHERE category_id LIKE :cat_id 
                ORDER BY parent_category_id ASC";
        $queryParams = [
            ':cat_id' => "%" . $catID . "%"
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function getAllSubsByParentId($parentID, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM $table 
                WHERE parent_category_id = :parent_category_id 
                ORDER BY category_id ASC";
        $queryParams = [
            ':parent_category_id' => $parentID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function save($id, $name, $parentID, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "INSERT INTO $table (category_id, parent_category_id, category_name) 
                VALUES (:category_id, :parent_category_id, :category_name) 
                ON DUPLICATE KEY UPDATE category_name = :category_name2";
        $queryParams = [
            ":category_id" => $id,
            ":parent_category_id" => $parentID,
            ":category_name" => $name,
            ':category_name2' => $name
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateMap($mapID, $id, $column)
    {
        $column = CHC::sanitize_table_name($column);
        $sql = "UPDATE categories_mapped 
                SET $column = :category_id 
                WHERE id = :id";
        $queryParams = [
            ':category_id' => $id,
            ':id' => $mapID
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function update($sku, $id, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "UPDATE $table SET category_id = :category_id WHERE sku = :sku";
        $queryParams = [
            ':category_id' => $id,
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function getMappedById($fromColumn, $toColumn, $categoryID)
    {
        $fromColumn = CHC::sanitize_table_name($fromColumn);
        $toColumn = CHC::sanitize_table_name($toColumn);
        $sql = "SELECT $toColumn 
                FROM categories_mapped 
                WHERE $fromColumn = :category_id";
        $queryParams = [
            ':category_id' => $categoryID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }
}