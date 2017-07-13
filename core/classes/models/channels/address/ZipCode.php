<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class ZipCode
{

    private $zipCode;
    private $stateID;
    private $zipID;

    public function __construct($zipCode, $stateID)
    {

        $this->zipCode = $zipCode;
        $this->stateID = $stateID;
        $this->zipID = ZipCode::searchOrInsert($this->zipCode, $this->stateID);
    }

    public function getZipCodeId()
    {
        return $this->zipID;
    }

    public static function getId($zip)
    {
        $sql = "SELECT id 
                FROM zip 
                WHERE zip.zip = :zip";
        $queryParams = [
            ':zip' => $zip
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($stateID, $zip)
    {
        $sql = "INSERT INTO zip (state_id, zip) 
                VALUES (:state_id, :zip) ";
        $queryParams = [
            ':state_id' => $stateID,
            ':zip' => $zip
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function searchOrInsert($zip, $stateID = '')
    {
        $zip = substr($zip, 0, 5); //Constrain ZIP to first 5 characters
        $zip_id = ZipCode::getId($zip);
        if (empty($zip_id)) {
            $zip_id = ZipCode::save($stateID, $zip);
        }
        return $zip_id;
    }
}