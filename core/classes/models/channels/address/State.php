<?php

namespace models\channels\address;


use models\ModelDB as MDB;

class State
{

    private $state;
    private $stateAbbr;
    private $stateID;

    public function __construct($state)
    {
        $this->setState($state);
        $this->setStateId($state);
    }

    private function setState($state)
    {
        $state = $this->longName($state);
        $this->state = strtoupper($state);
    }

    private function setStateId($state)
    {
        $this->stateID = State::getIdByAbbr($state);
    }

    public function getId(): int
    {
        return $this->stateID;
    }

    public static function getIdByAbbr($stateAbbreviation): int
    {
        $sql = "SELECT id 
                FROM state 
                WHERE state.abbr = :state";
        $queryParams = [
            ':state' => $stateAbbreviation
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getAbbr($state): string
    {
        $sql = "SELECT abbr 
                FROM state 
                WHERE state.name = :state";
        $queryParams = [
            ':state' => $state
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    /**
     * @param $state
     * @return bool|string
     */
    public function longName($state): string
    {
        if (strlen($state) > 2) {
            $state = State::getAbbr(standardCase($state));
        }
        return $state;
    }
}