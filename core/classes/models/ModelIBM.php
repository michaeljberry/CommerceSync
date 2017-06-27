<?php

namespace models;

use connect\IBMDB;

class ModelIBM
{
    private $db;

    public function __construct()
    {
        $this->db = IBMDB::instance();
    }

    public static function query($sql, $args = null, $returnMethod = '', $returnMethodParams = null)
    {
        $results = '';
        $query = IBMDB::run($sql, $args);
        switch ($returnMethod) {
            case 'fetchAll':
                $results = $query->fetchAll($returnMethodParams);
                break;
            case 'fetch':
                $results = $query->fetch();
                break;
            case 'fetchColumn':
                $results = $query->fetchColumn();
                break;
            case 'rowCount':
                $results = $query->rowCount();
                break;
        }
        return $results;
    }
}