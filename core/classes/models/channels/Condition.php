<?php

namespace models\channels;

use models\ModelDB as MDB;

class Condition
{

    public static function getId($condition)
    {
        $sql = "SELECT id 
                FROM sync.condition 
                WHERE condition.condition = :condition";
        $query_params = [
            ':condition' => $condition
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function searchOrInsert($condition)
    {
        $id = Condition::getId($condition);
        if (empty($id)) {
            return $condition;
        }
        return $id;
    }
}