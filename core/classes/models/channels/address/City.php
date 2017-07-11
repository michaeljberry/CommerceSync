<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class City
{
    public static function getId($city, $stateID)
    {
        $sql = "SELECT id 
                FROM city 
                WHERE city.name = :city 
                AND state_id = :state_id";
        $queryParams = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $stateID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($city, $stateID)
    {
        $sql = "INSERT INTO city (state_id, name) 
                VALUES (:state_id, :city)";
        $queryParams = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $stateID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }
}