<?php

namespace Amazon;

use Crypt;
use models\ModelDB as MDB;

class Amazon
{
    /**
     * Sanitize the columns that are allowed to be updated
     *
     * @param $col
     * @return string
     */
    public function sanitizeColumnName($col)
    {
        switch ($col) {
            case $col == "merchantid":
                $column = 'merchantid';
                break;
            case $col == "marketplaceid":
                $column = 'marketplaceid';
                break;
            case $col == "aws_access_key":
                $column = 'aws_access_key';
                break;
            case $col == "secret_key":
                $column = 'secret_key';
                break;
        }
        return $column;
    }

    /**
     * Get DB ID for Amazon listings using Amazon's listing ID.
     *
     * @param $store_listing_id
     * @return bool|string
     */
    public function getListing($store_listing_id)
    {
        $sql = "SELECT id FROM listing_amazon WHERE store_listing_id = :store_listing_id";
        $query_params = array(
            'store_listing_id' => $store_listing_id
        );
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function saveAppInfo($store_id, $merchant_id, $marketplace_id, $aws_access_key, $secret_key)
    {
        $sql = "INSERT INTO api_amazon (store_id, merchantid, marketplaceid, aws_access_key, secret_key) VALUES (:store_id, :merchantid, :marketplaceid, :aws_access_key, :secret_key)";
        $query_params = array(
            ":store_id" => $store_id,
            ":merchantid" => Crypt::encrypt($merchant_id),
            ":marketplaceid" => Crypt::encrypt($marketplace_id),
            ":aws_access_key" => Crypt::encrypt($aws_access_key),
            ":secret_key" => Crypt::encrypt($secret_key)
        );
        MDB::query($sql, $query_params);
    }

    public function updateAppInfo($storeID, $column, $id)
    {
        $column = $this->sanitizeColumnName($column);
        $sql = "UPDATE api_amazon SET $column = :id WHERE store_id = :store_id";
        $query_params = array(
            ':id' => Crypt::encrypt($id),
            ':store_id' => $storeID
        );
        MDB::query($sql, $query_params);
    }

    public static function getApiOrderDays()
    {
        $sql = "SELECT api_pullfrom, api_pullto FROM api_amazon WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => AmazonClient::getStoreId()
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    public static function setApiOrderDays($from, $to)
    {
        $sql = "UPDATE api_amazon SET api_pullfrom = :api_pullfrom, api_pullto = :api_pullto WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => AmazonClient::getStoreId(),
            ':api_pullfrom' => $from,
            ':api_pullto' => $to
        ];
        MDB::query($sql, $query_params);
    }

}
