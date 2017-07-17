<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class ZipCode
{

    private $zipCode;
    private $stateID;
    private $zipCodeID;

    public function __construct($zipCode, State $state)
    {

        $this->setZipCode($zipCode);
        $this->setStateId($state);
        $this->zipCodeID = ZipCode::searchOrInsert($this->zipCode, $this->stateID);
    }

    private function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    private function setStateId(State $state)
    {
        $this->stateID = $state->getId();
    }

    public function getId(): int
    {
        return $this->zipCodeID;
    }

    public static function getIdByZip($zip): int
    {
        $sql = "SELECT id 
                FROM zip 
                WHERE zip.zip = :zip";
        $queryParams = [
            ':zip' => $zip
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($stateID, $zip): int
    {
        $sql = "INSERT INTO zip (state_id, zip) 
                VALUES (:state_id, :zip) ";
        $queryParams = [
            ':state_id' => $stateID,
            ':zip' => $zip
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($zip, $stateID = ''): int
    {
        $zip = substr($zip, 0, 5); //Constrain ZIP to first 5 characters
        $zip_id = ZipCode::getIdByZip($zip);
        if (empty($zip_id)) {
            $zip_id = ZipCode::save($stateID, $zip);
        }
        return $zip_id;
    }
}