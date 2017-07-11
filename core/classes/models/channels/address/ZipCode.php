<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class ZipCode
{
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
}