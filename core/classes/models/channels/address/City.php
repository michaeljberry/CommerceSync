<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class City
{

    private $city;
    private $stateID;
    public $cityID;

    public function __construct($city, $stateID)
    {

        $this->city = $city;
        $this->stateID = $stateID;
        $this->cityID = City::searchOrInsert($this->city, $this->stateID);
    }

    public function getCityId()
    {
        return $this->cityID;
    }

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

    public static function searchOrInsert($city, $stateID)
    {
        $cityID = City::getId($city, $stateID);
        if (empty($cityID)) {
            $cityID = City::save($city, $stateID);
        }
        return $cityID;
    }
}