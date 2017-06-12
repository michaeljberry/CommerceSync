<?php

namespace models;

use connect\DB;

class modelDB
{

    private $db;

    public function __construct(){
        $this->db = DB::instance();
    }

    //To put every insert/update statement inside a transaction
    public static function insert_transact($sql, $query_params){
        try {
            DB::beginTransaction();
            DB::run($sql, $query_params);
            $id = DB::lastInsertId();
            DB::commit();
            return $id;
        } catch (Exception $e){
            DB::rollback();
            die($e->getMessage());
        }
    }

    public static function query($sql, $args = null, $returnMethod = '', $returnMethodParams = null)
    {
        $bool = false;
        if(strpos($sql, 'INSERT') !== false || strpos($sql, 'UPDATE') !== false){
            $id = static::insert_transact($sql, $args);
            if($id){
                $bool = true;
            }
        }else {
            $query = DB::run($sql, $args);
        }
        switch ($returnMethod){
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
            case 'id':
                $results = $id;
                break;
            case 'boolean':
                $results = $bool;
                break;
        }
        return $results;
    }
}