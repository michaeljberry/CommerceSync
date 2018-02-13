<?php

namespace Walmart;

use models\ModelDB as MDB;
use ecommerce\ChannelInterface;

class Walmart implements ChannelInterface
{

    private static $apiTable = 'api_walmart';
    private static $channel = 'Walmart';
    private static $userID;

    public function __construct($userID)
    {
        static::setUserId($userID);
    }

    protected static function setUserId($userID)
    {
        static::$userID = $userID;
    }

    protected static function getUserId()
    {
        return static::$userID;
    }

    protected static function getApiTable()
    {
        return static::$apiTable;
    }

    protected static function getChannelName()
    {
        return static::$channel;
    }

    public function get_wm_app_info($user_id)
    {
        $sql = "SELECT wm.id, store_id, wm.consumer_id, wm.secret_key, wm.api_header FROM api_walmart AS wm INNER JOIN store ON wm.store_id = store.id INNER JOIN account ON account.company_id = store.company_id INNER JOIN channel ON channel.id = store.channel_id WHERE account.id = :user_id AND channel.name = 'Walmart'";
        $query_params = array(
            ':user_id' => $user_id
        );
        return MDB::query($sql, $query_params, 'fetch');
    }

    public function save_app_info($crypt, $store_id, $consumer_id, $secret_key)
    {
        $sql = "INSERT INTO api_walmart (store_id, consumer_id, secret_key) VALUES (:store_id, :consumer_id, :secret_key)";
        $query_params = array(
            ":store_id" => $store_id,
            ":consumer_id" => $crypt->encrypt($consumer_id),
            ":secret_key" => $crypt->encrypt($secret_key)
        );
        MDB::query($sql, $query_params);
    }

    public function update_app_info($crypt, $store_id, $consumer_id, $secret_key, $api_header)
    {
        $sql = "UPDATE api_walmart SET consumer_id = :consumer_id, secret_key = :secret_key, api_header = :api_header WHERE store_id = :store_id";
        $query_params = [
            ':consumer_id' => $crypt->encrypt($consumer_id),
            ':secret_key' => $crypt->encrypt($secret_key),
            ':api_header' => $api_header,
            ':store_id' => $store_id
        ];
        MDB::query($sql, $query_params);
    }

    public static function getApiOrderDays()
    {
        $sql = "SELECT api_days FROM api_walmart WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => WalmartClient::getStoreId()
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function setApiOrderDays($days)
    {
        $sql = "UPDATE api_walmart SET api_days = :api_days WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => WalmartClient::getStoreId(),
            ':api_days' => $days
        ];
        MDB::query($sql, $query_params);
    }
}
