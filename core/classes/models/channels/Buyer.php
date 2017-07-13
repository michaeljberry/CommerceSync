<?php

namespace models\channels;

use models\channels\address\City;
use models\channels\address\State;
use models\channels\address\ZipCode;
use models\ModelDB as MDB;

class Buyer
{

    private $firstName;
    private $lastName;
    private $shippingAddress;
    private $shippingAddress2;
    private $cityID;
    private $stateID;
    private $zipID;
    private $buyerID;
    private $email;

    public function __construct(
        $firstName,
        $lastName,
        $shippingAddress,
        $shippingAddress2,
        $city,
        $state,
        $zipCode,
        $email = null
    ) {

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->shippingAddress = standardCase($shippingAddress);
        $this->shippingAddress2 = standardCase($shippingAddress2);
        $this->stateID = (new State($state))->getStateId();
        $this->cityID = (new City($city, $this->stateID))->getCityId();
        $this->zipID = (new ZipCode($zipCode, $this->stateID))->getZipCodeId();
        $this->email = $email;
        $this->buyerID = Buyer::searchOrInsert($this->firstName, $this->lastName, $this->shippingAddress,
            $this->shippingAddress2, $this->cityID, $this->stateID, $this->zipID);
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