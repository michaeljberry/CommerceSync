<?php

namespace models\channels;

use models\ModelDB as MDB;

class Address
{

    public static function zipSoi($zip, $state_id = '')
    {
        $zip = substr($zip, 0, 5); //Constrain ZIP to first 5 characters
        $sql = "SELECT id 
                FROM zip 
                WHERE zip.zip = :zip";
        $queryParams = [
            ':zip' => $zip
        ];
        $zip_id = MDB::query($sql, $queryParams, 'fetchColumn');
        if (empty($zip_id)) {
            $sql = "INSERT INTO zip (state_id, zip) 
                    VALUES (:state_id, :zip) ";
            $queryParams = [
                ':state_id' => $state_id,
                ':zip' => $zip
            ];
            $zip_id = MDB::query($sql, $queryParams, 'id');
        }
        return $zip_id;
    }

    public static function stateId($state_abbr)
    {
        $sql = "SELECT id 
                FROM state 
                WHERE state.abbr = :state";
        $queryParams = [
            ':state' => $state_abbr
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function stateToAbbr($state)
    {
        $sql = "SELECT abbr 
                FROM state 
                WHERE state.name = :state";
        $queryParams = [
            ':state' => $state
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
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