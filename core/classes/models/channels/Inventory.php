<?php

namespace models\channels;


use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class Inventory
{

    public static function getUpdated($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT tb.sku, tb.inventory_level AS qty 
                FROM $table tb 
                WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 45 MINUTE)";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getUpdatedListing($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty";
        if ($table === 'listing_amazon') {
            $sql .= ",tb.asin1";
        }
        $sql .= ", sk.sku FROM stock st JOIN $table tb ON tb.stock_id = st.id LEFT OUTER JOIN sku sk on sk.id = st.sku_id"; //WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getUpdatedBySKU($table, $sku)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty 
                FROM stock st 
                JOIN $table tb ON tb.stock_id = st.id 
                WHERE tb.sku = :sku";
        $query_params = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    public static function get_inventory_for_update($table, $sku = null)
    {
        $table = CHC::sanitize_table_name($table);
        if (empty($sku)) {
            $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty";
            if ($table === 'listing_amazon') {
                $sql .= ",tb.asin1";
            }
            $sql .= ", sk.sku FROM stock st JOIN $table tb ON tb.stock_id = st.id LEFT OUTER JOIN sku sk on sk.id = st.sku_id"; //WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
            return MDB::query($sql, [], 'fetchAll');
        } else {
            $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty FROM stock st JOIN $table tb ON tb.stock_id = st.id WHERE tb.sku = :sku";
            $query_params = [
                ':sku' => $sku
            ];
            return MDB::query($sql, $query_params, 'fetch');
        }
    }
}