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
    private $country;
    private $phone;
    private $email;

    public function __construct(
        $firstName,
        $lastName,
        $streetAddress,
        $streetAddress2,
        $city,
        $state,
        $zipCode,
        $country,
        $phone,
        $email = null
    ) {

        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setStreetAddress($streetAddress);
        $this->setStreetAddress2($streetAddress2);
        $this->setState($state);
        $this->setCity($city, $this->getState());
        $this->setZipCode($zipCode, $this->getState());
        $this->setCountry($country);
        $this->setPhone($phone);
        $this->setEmail($email);
        $this->setId();
    }

    private function setFirstName($firstName)
    {
        $this->firstName = standardCase($firstName);
    }

    private function setLastName($lastName)
    {
        $this->lastName = standardCase($lastName);
    }

    private function setStreetAddress($streetAddress)
    {
        $this->streetAddress = standardCase($streetAddress);
    }

    private function setStreetAddress2($streetAddress2)
    {
        $this->streetAddress2 = standardCase($streetAddress2);
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

    private function setCountry($country)
    {
        $this->country = Address::countryCode($country);
    }

    private function setPhone($phone)
    {
        $this->phone = $phone;
    }

    private function setEmail($email)
    {
        $this->email = $email;
    }

    private function setId()
    {
        $this->buyerID = Buyer::searchOrInsert($this->getFirstName(), $this->getLastName(), $this->getStreetAddress(),
            $this->getStreetAddress2(), $this->getCity()->getId(), $this->getState()->getId(),
            $this->getZipCode()->getId());
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    public function getStreetAddress2(): string
    {
        return $this->streetAddress2;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getZipCode(): ZipCode
    {
        return $this->zipCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): int
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