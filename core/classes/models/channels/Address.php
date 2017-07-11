<?php

namespace models\channels;

use models\channels\address\ZipCode;
use models\ModelDB as MDB;

class Address
{

    public static function searchOrInsertZip($zip, $stateID = '')
    {
        $zip = substr($zip, 0, 5); //Constrain ZIP to first 5 characters
        $zip_id = ZipCode::getId($zip);
        if (empty($zip_id)) {
            $zip_id = ZipCode::save($stateID, $zip);
        }
        return $zip_id;
    }

    public static function citySoi($city, $state_id)
    {
        $sql = "SELECT id 
                FROM city 
                WHERE city.name = :city 
                AND state_id = :state_id";
        $queryParams = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $state_id
        ];
        $city_id = MDB::query($sql, $queryParams, 'fetchColumn');
        if (empty($city_id)) {
            $sql = "INSERT INTO city (state_id, name) 
                    VALUES (:state_id, :city)";
            $queryParams = [
                ':city' => ucwords(strtolower($city)),
                ':state_id' => $state_id
            ];
            $city_id = MDB::query($sql, $queryParams, 'id');
        }
        return $city_id;
    }
}