<?php

namespace models\channels\address;

use models\ModelDB as MDB;

class Address
{

    public static function searchOrInsertZip($zip, $stateID = '')
    {
        $zip = substr($zip, 0, 5); //Constrain ZIP to first 5 characters
        $zip_id = ZipCode::getId($zip);
        if (empty($zip_id)) {
            $zip_id = ZipCode::save($stateID, $zip);
        }
        return $zip_id;
    }

    public static function searchOrInsertCity($city, $stateID)
    {
        $cityID = City::getId($city, $stateID);
        if (empty($cityID)) {
            $cityID = City::save($city, $stateID);
        }
        return $cityID;
    }
}