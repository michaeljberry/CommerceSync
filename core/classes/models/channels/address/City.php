<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class City
{

    private $city;
    public $cityID;

    public function __construct($city, State $state)
    {
        $this->setCity($city);
        $this->setCityId($state);
    }

    private function setCity($city)
    {
        $this->city = standardCase($city);
    }

    private function setCityId(State $state)
    {
        $this->cityID = City::searchOrInsert($this->getId(), $state->getId());
    }

    public function getId()
    {
        return $this->cityID;
    }

    public static function getIdByState($city, $stateID): int
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

    public static function save($city, $stateID): int
    {
        $sql = "INSERT INTO city (state_id, name) 
                VALUES (:state_id, :city)";
        $queryParams = [
            ':city' => ucwords(strtolower($city)),
            ':state_id' => $stateID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($city, $stateID): int
    {
        $cityID = City::getIdByState($city, $stateID);
        if (empty($cityID)) {
            $cityID = City::save($city, $stateID);
        }
        return $cityID;
    }
}