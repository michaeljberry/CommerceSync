<?php

namespace models\channels;

use IBM;
use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class Channel
{

    public static function prepareColumnList($table, $columns)
    {
        return $table . '.' . implode(",$table.", $columns);
    }

    public static function prepareColumnInsert($columns)
    {
        return implode(',', $columns);
    }

    public static function prepareColumnValues($columns)
    {
        return ':' . implode(',:', $columns);
    }

    public static function prepareColumnParams($columns)
    {
        $params = [];

        foreach ($columns as $column){
            $params[':' . $column] = $column;
        }

        return $params;
    }

    public static function getAppInfo($user_id, $table, $channel, $columns)
    {
        $columnSelect = Channel::prepareColumnList($table, $columns);

        $sql = "SELECT $columnSelect
                FROM $table
                INNER JOIN store ON $table.store_id = store.id 
                INNER JOIN account ON account.company_id = store.company_id 
                INNER JOIN channel ON channel.id = store.channel_id 
                WHERE account.id = :user_id AND channel.name = :channel";
        $queryParams = [
            'user_id' => $user_id,
            'channel' => $channel
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function saveAppInfo($storeID, $table, $columns)
    {
        $columnInsert = Channel::prepareColumnInsert($columns);
        $columnValues = Channel::prepareColumnValues($columns);
        $columnParams = Channel::prepareColumnParams($columns);

        $table = CHC::sanitizeAPITableName($table);

        $sql = "INSERT INTO $table ($columnInsert) 
                VALUES ($columnValues)
                ON DUPLICATE KEY UPDATE ";
        $queryParams = [
            ":store_id" => $storeID,
        ];
        $queryParams = array_merge($queryParams, $columnParams);

        return MDB::query($sql, $queryParams, 'id');
    }

    public static function getAccounts($name)
    {
        $sql = "SELECT co_one_acct, co_two_acct 
                FROM channel 
                WHERE channel.name = :name";
        $queryParams = [
            ':name' => $name
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function getAccountNumbersBySku($name, $sku)
    {
        $accounts = Channel::getAccounts($name);
        $companyOneAccount = $accounts['co_one_acct'];
        $companyTwoAccount = $accounts['co_two_acct'];
        $inventory = IBM::findInventory($sku, $name);
        $companyOneQty = $inventory['CO_ONE'];
        $companyTwoQty = $inventory['CO_TWO'];
        if (!empty($companyOneQty)) {
            $number = $companyOneAccount;
        } elseif (!empty($companyTwoQty)) {
            $number = $companyTwoAccount;
        } else {
            $number = $companyOneAccount;
        }
        return $number;
    }

    public static function getAccountNumbers($channel)
    {
        $companyNumbers = Channel::getAccounts($channel);
        return implode(",", $companyNumbers);

    }
}