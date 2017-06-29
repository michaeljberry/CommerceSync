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
        $query_params = [
            ':zip' => $zip
        ];
        $zip_id = MDB::query($sql, $query_params, 'fetchColumn');
        if (empty($zip_id)) {
            $sql = "INSERT INTO zip (state_id, zip) 
                    VALUES (:state_id, :zip) ";
            $query_params = [
                ':state_id' => $state_id,
                ':zip' => $zip
            ];
            $zip_id = MDB::query($sql, $query_params, 'id');
        }
        return $zip_id;
    }

    public static function stateId($state_abbr)
    {
        $sql = "SELECT id 
                FROM state 
                WHERE state.abbr = :state";
        $query_params = [
            ':state' => $state_abbr
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function stateToAbbr($state)
    {
        $sql = "SELECT abbr 
                FROM state 
                WHERE state.name = :state";
        $query_params = [
            ':state' => $state
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function citySoi($city, $state_id)
    {
        $sql = "SELECT id 
                FROM city 
                WHERE city.name = :city 
                AND state_id = :state_id";
        $query_params = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $state_id
        ];
        $city_id = MDB::query($sql, $query_params, 'fetchColumn');
        if (empty($city_id)) {
            $sql = "INSERT INTO city (state_id, name) 
                    VALUES (:state_id, :city)";
            $query_params = [
                ':city' => ucwords(strtolower($city)),
                ':state_id' => $state_id
            ];
            $city_id = MDB::query($sql, $query_params, 'id');
        }
        return $city_id;
    }
}