<?php

namespace models\channels;


use models\ModelDB as MDB;
use PDO;

class Tax
{

    public static function getCompanyInfo($companyID)
    {
        $sql = "SELECT s.abbr, t.tax_rate, t.tax_line_name, t.shipping_taxed 
                FROM taxes t 
                INNER JOIN state s ON s.id = t.state_id 
                WHERE company_id = :company_id";
        $queryParams = [
            ':company_id' => $companyID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll', PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

}