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

    public static function getById($categoryID)
    {
        $sql = "SELECT categories_amazon_id AS id 
                FROM categories_mapped 
                WHERE categories_ebay_id = :cat";
        $query_params = [
            ':cat' => $categoryID
        ];
        return MDB::query($sql, $query_params, 'fetchAll');
    }

    public static function getMappable($categoryID = null)
    {
        if (empty($categoryID)) {
            return Category::get();
        }
        return Category::getById($categoryID);
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

    public static function getEbay($cat_id)
    {
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM categories_ebay 
                WHERE category_id LIKE :cat_id 
                ORDER BY parent_category_id ASC";
        $query_params = [
            ':cat_id' => "%" . $cat_id . "%"
        ];
        return MDB::query($sql, $query_params, 'fetchAll');
    }

    public static function getAllSubCategories($parentCategory, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT category_id, parent_category_id, category_name 
                FROM $table 
                WHERE parent_category_id = :parent_category_id 
                ORDER BY category_id ASC";
        $query_params = [
            ':parent_category' => $parentCategory
        ];
        return MDB::query($sql, $query_params, 'fetchAll');
    }

    public static function save($category_id, $category_name, $category_parent_id, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "INSERT INTO $table (category_id, parent_category_id, category_name) 
                VALUES (:category_id, :parent_category_id, :category_name) 
                ON DUPLICATE KEY UPDATE category_name = :category_name2";
        $query_params = [
            ":category_id" => $category_id,
            ":parent_category_id" => $category_parent_id,
            ":category_name" => $category_name,
            ':category_name2' => $category_name
        ];
        return MDB::query($sql, $query_params, 'id');
    }

    public static function updateMap($id, $category_id, $column)
    {
        $column = CHC::sanitize_table_name($column);
        $sql = "UPDATE categories_mapped 
                SET $column = :category_id 
                WHERE id = :id";
        $query_params = [
            ':category_id' => $category_id,
            ':id' => $id
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }
}