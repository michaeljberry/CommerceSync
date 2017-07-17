<?php

namespace models\channels;

use models\channels\address\Address;
use models\channels\address\City;
use models\channels\address\State;
use models\channels\address\ZipCode;
use models\ModelDB as MDB;

class Buyer
{

    private $firstName;
    private $lastName;
    private $streetAddress;
    private $streetAddress2;
    private $city;
    private $state;
    private $zipCode;
    private $buyerID;
    private $email;
    private $country;

    public function __construct(
        $firstName,
        $lastName,
        $streetAddress,
        $streetAddress2,
        $city,
        $state,
        $zipCode,
        $country,
        $email = null
    ) {

        $this->firstName = standardCase($firstName);
        $this->lastName = standardCase($lastName);
        $this->streetAddress = standardCase($streetAddress);
        $this->streetAddress2 = standardCase($streetAddress2);
        $this->setState($state);
        $this->setCity($city, $this->state);
        $this->setZipCode($zipCode, $this->state);
        $this->country = Address::countryCode($country);
        $this->email = $email;
        $this->buyerID = Buyer::searchOrInsert($this->firstName, $this->lastName, $this->streetAddress,
            $this->streetAddress2, $this->city->getId(), $this->state->getId(), $this->zipCode->getId());
        $this->country = $country;
    }

    private function setState($state)
    {
        $this->state = new State($state);
    }

    private function setCity($city, State $state)
    {
        $this->city = new City($city, $state);
    }

    private function setZipCode($zipCode, State $state)
    {
        $this->zipCode = new ZipCode($zipCode, $state);
    }

    public function getState()
    {
        return $this->state;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function getBuyerId()
    {
        return $this->buyerID;
    }

    public static function getIdByBuyer($firstName, $lastName, $streetAddress, $zipID): int
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

    public static function save($firstName, $lastName, $streetAddress, $streetAddress2, $cityID, $stateID, $zipID): int
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
    ): int {
        $id = Buyer::getIdByBuyer($firstName, $lastName, $streetAddress, $zipID);
        if (empty($id)) {
            $id = Buyer::save($firstName, $lastName, $streetAddress, $streetAddress2, $cityID, $stateID, $zipID);
        }
        return $id;
    }
}