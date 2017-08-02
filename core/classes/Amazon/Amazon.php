<?php

namespace Amazon;

use Crypt;
use models\channels\ChannelAPI;
use models\ModelDB as MDB;

class Amazon //extends ChannelAPI
{
    protected $apiTable = 'api_amazon';
    protected $apiColumns = [

    ];

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
     * @param $storeListingID
     * @return bool|string
     */
    public function getListing($storeListingID)
    {
        $sql = "SELECT id FROM listing_amazon WHERE store_listing_id = :store_listing_id";
        $query_params = array(
            'store_listing_id' => $storeListingID
        );
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public function saveAppInfo($storeID, $merchantID, $marketplaceID, $awsAccessKey, $secretKey)
    {
        $sql = "INSERT INTO api_amazon (store_id, merchantid, marketplaceid, aws_access_key, secret_key) VALUES (:store_id, :merchantid, :marketplaceid, :aws_access_key, :secret_key)";
        $query_params = array(
            ":store_id" => $storeID,
            ":merchantid" => Crypt::encrypt($merchantID),
            ":marketplaceid" => Crypt::encrypt($marketplaceID),
            ":aws_access_key" => Crypt::encrypt($awsAccessKey),
            ":secret_key" => Crypt::encrypt($secretKey)
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
        $sql = "SELECT api_from, api_to FROM api_amazon WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => AmazonClient::getStoreId()
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    public static function updateApiOrderDays($from, $to = null)
    {
        $sql = "UPDATE api_amazon SET api_from = :api_from, api_to = :api_to WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => AmazonClient::getStoreId(),
            ':api_from' => $from,
            ':api_to' => $to
        ];
        MDB::query($sql, $query_params);
    }

}
