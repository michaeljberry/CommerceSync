<?php

namespace models\channels;


use controllers\channels\ChannelHelperController as CHC;
use ecommerce\Ecommerce;
use models\ModelDB as MDB;

class Listing
{

    public static function getAllFromChannels() //, $offset, $limit
    {
        $sql = "SELECT a.sku, a.asin1 AS am_list, b.store_listing_id AS bc_list, e.store_listing_id AS eb_list, r.store_listing_id AS rev_list 
                FROM sync.listing_amazon a 
                LEFT JOIN listing_bigcommerce b ON b.sku = a.sku 
                LEFT JOIN listing_ebay e ON e.sku = a.sku 
                LEFT JOIN listing_reverb r ON r.sku = a.sku 
                ORDER BY sku ASC";
        // LIMIT $offset, $limit
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getBySKU($sku, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT * 
                FROM $table 
                WHERE sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function getPriceBySKU($sku, $table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT price 
                FROM $table 
                WHERE sku = :sku 
                AND override_price = 0";
        $queryParams = [
            'sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getUpdatedBySKU($table, $sku)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty 
                FROM stock st 
                JOIN $table tb ON tb.stock_id = st.id 
                WHERE tb.sku = :sku";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function getAll($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT st.id, st.sku_id, tb.inventory_level AS stock_qty";
        if ($table === 'listing_amazon') {
            $sql .= ",tb.asin1";
        }
        $sql .= ", sk.sku 
                FROM stock st 
                JOIN $table tb ON tb.stock_id = st.id 
                LEFT OUTER JOIN sku sk on sk.id = st.sku_id";
        //WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getUpdated($table)
    {
        $table = CHC::sanitize_table_name($table);
        $sql = "SELECT tb.sku, tb.inventory_level AS qty 
                FROM $table tb 
                WHERE tb.last_edited >= DATE_SUB(NOW(), INTERVAL 45 MINUTE)";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getStoreIdByStockId($stockID, $table)
    {
        $table_col = CHC::sanitize_table_name($table);
        $sql = "SELECT store_listing_id 
                FROM $table_col 
                WHERE stock_id = :stock_id";
        $queryParams = [
            ':stock_id' => $stockID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getIdBySKU($sku, $table)
    {
        $table_col = CHC::sanitize_table_name($table);
        $sql = "SELECT store_listing_id 
                FROM $table_col 
                WHERE stock_id = :stock_id";
        $queryParams = [
            ':sku' => $sku
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getId($table, $stockID, $storeID)
    {
        $sql = "SELECT id FROM $table WHERE stock_id = :stock_id AND store_id = :store_id";
        $queryParams = [
            ':stock_id' => $stockID,
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($table, $columns, $values, $updateString, $queryParams)
    {
        $sql = "INSERT INTO $table ($columns) 
                VALUES ($values) 
                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id),$updateString";
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($table, $storeID, $stockID, $channelArray, $update = false)
    {
        $table = CHC::sanitize_table_name($table);
        $listingID = Listing::getId($table, $stockID, $storeID);
        if ($update) {
            $returnArray = Ecommerce::prepare_arrays($channelArray);
            $columns = $returnArray[0];
            $values = $returnArray[1];
            $updateString = $returnArray[2];
            $queryParams = $returnArray[3];

            $listingID = Listing::save($table, $columns, $values, $updateString, $queryParams);
        }
        return $listingID;
    }
}