<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

abstract class ChannelAPI extends Channel
{
    protected $apiTable;
    protected $apiColumns;

    static function getApiOrderDays($apiTable, $apiColumns, $storeID)
    {
        $table = CHC::sanitizeAPITableName($apiTable);
        $columnSelect = Channel::prepareColumnList($table, $apiColumns);
        $sql = "SELECT $columnSelect FROM $table WHERE store_id = :store_id";
        $query_params = [
            ':store_id' => $storeID
        ];
        return MDB::query($sql, $query_params, 'fetch');
    }

    static function updateApiOrderDays($apiTable, $apiColumns)
    {
        return parent::insertOrUpdate($apiTable, $apiColumns);
    }
}
