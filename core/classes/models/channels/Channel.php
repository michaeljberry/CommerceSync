<?php

namespace models\channels;

use models\ModelDB as MDB;

class Channel
{
    public static function getAppInfo($user_id, $table, $channel, $columns)
    {
        $columnSelect = $table . '.' . implode(",$table.", $columns);
        $queryParams = [
            'user_id' => $user_id,
            'channel' => $channel
        ];
        $sql = "SELECT $columnSelect
                FROM $table
                INNER JOIN store ON $table.store_id = store.id 
                INNER JOIN account ON account.company_id = store.company_id 
                INNER JOIN channel ON channel.id = store.channel_id 
                WHERE account.id = :user_id AND channel.name = :channel";
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function setAppInfo($store_id, $dev_id, $app_id, $cert_id, $token)
    {
        $sql = "INSERT INTO api_ebay (store_id, devid, appid, certid, token) 
                VALUES (:store_id, :devid, :appid, :certid, :token)";
        $queryParams = [
            ":store_id" => $store_id,
            ":devid" => Crypt::encrypt($dev_id),
            ":appid" => Crypt::encrypt($app_id),
            ":certid" => Crypt::encrypt($cert_id),
            ":token" => Crypt::encrypt($token)
        ];
        return MDB::query($sql, $queryParams, 'id');
    }
}