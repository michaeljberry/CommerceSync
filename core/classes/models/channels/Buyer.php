<?php

namespace models\channels;

use models\ModelDB as MDB;

class Buyer
{

    public static function getIdByBuyer($firstName, $lastName, $streetAddress, $zipID)
    {
        $sql = "SELECT id 
                FROM customer 
                WHERE first_name = :first_name 
                AND last_name = :last_name 
                AND street_address = :street_address 
                AND zip_id = :zip_id";
        $queryParams = [
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':street_address' => $streetAddress,
            ':zip_id' => $zipID
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($firstName, $lastName, $streetAddress, $streetAddress2, $cityID, $stateID, $zipID)
    {
        $sql = "INSERT INTO customer (first_name, last_name, street_address, street_address2, city_id, state_id, zip_id) 
                VALUES (:first_name, :last_name, :street_address, :street_address2, :city_id, :state_id, :zip_id)";
        $queryParams = [
            ":first_name" => $firstName,
            ":last_name" => $lastName,
            ":street_address" => $streetAddress,
            ":street_address2" => $streetAddress2,
            ":city_id" => $cityID,
            ":state_id" => $stateID,
            ":zip_id" => $zipID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert(
        $firstName,
        $lastName,
        $streetAddress,
        $streetAddress2,
        $cityID,
        $stateID,
        $zipID
    ) {
        $id = Buyer::getIdByBuyer($firstName, $lastName, $streetAddress, $zipID);
        if (empty($id)) {
            $id = Buyer::save($firstName, $lastName, $streetAddress, $streetAddress2, $cityID, $stateID, $zipID);
        }
        return $id;
    }
}