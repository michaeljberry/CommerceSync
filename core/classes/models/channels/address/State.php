<?php

namespace models\channels\address;


use models\ModelDB as MDB;

class State
{

    private $state;
    private $stateID;

    public function __construct($state)
    {
        $this->state = $state;
        $this->stateID = State::getIdByAbbr($state);
    }

    public function getStateId()
    {
        return $this->stateID;
    }

    public static function getIdByAbbr($stateAbbreviation)
    {
        $sql = "SELECT id 
                FROM state 
                WHERE state.abbr = :state";
        $queryParams = [
            ':state' => $stateAbbreviation
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getAbbr($state)
    {
        $sql = "SELECT abbr 
                FROM state 
                WHERE state.name = :state";
        $queryParams = [
            ':state' => $state
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }
}