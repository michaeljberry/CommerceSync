<?php

namespace QB;

use ecommerce\Ecommerce;

class querybuilder
{
    public static function arrayToQuery($sqlArray)
    {
        $tableAbbreviation = 'a';
        $tableAbbreviationArray = [];
        $query = '';
        $response = [];
        if(array_key_exists('SELECT', $sqlArray)){
            $response = self::selectQuery($sqlArray['SELECT'], $tableAbbreviationArray, $tableAbbreviation);
            $tableAbbreviationArray = $response['tableAbbreviationArray'];
            $tableAbbreviation = $response['tableAbbreviation'];
            $query .= $response['query'];

            if(array_key_exists('JOIN', $sqlArray)){
                $response = self::joinQuery($sqlArray['JOIN'], $tableAbbreviationArray, $tableAbbreviation, $query);
                $tableAbbreviationArray = $response['tableAbbreviationArray'];
                $tableAbbreviation = $response['tableAbbreviation'];
                $query = $response['query'];
            }

            if(array_key_exists('WHERE', $sqlArray)){
                $query .= self::whereQuery($sqlArray['WHERE'], $tableAbbreviationArray);
            }

            if(array_key_exists('GROUP BY', $sqlArray)){
                $query .= self::groupByQuery($sqlArray['GROUP BY'], $tableAbbreviationArray);
            }

            if(array_key_exists('ORDER BY', $sqlArray)){
                $query .= self::orderByQuery($sqlArray['ORDER BY'], $tableAbbreviationArray);
            }

        }elseif (array_key_exists('INSERT', $sqlArray)){
            $response = self::insertQuery($sqlArray['INSERT']);
        }elseif (array_key_exists('UPDATE', $sqlArray)){
            $response = self::updateQuery($sqlArray['UPDATE']);
        }elseif (array_key_exists('DELETE', $sqlArray)){
            $response = self::deleteQuery($sqlArray['DELETE']);
        }

        return $query;
    }

    protected static function whereQuery($whereArray, $tableAbbreviationArray = [], &$where = 'WHERE ', &$i = 0)
    {
        if(is_array(reset($whereArray))) {
            foreach ($whereArray as $key => $value) {
                if (is_array($value) && is_array(reset($value))) {
                    $where .= "(";
                    self::whereQuery($value, $tableAbbreviationArray, $where, $i);
                    $where .= ")";
                } elseif ((is_string($value) && ($value === 'AND' || $value === 'OR')) || is_array($value)) {
                    if (is_string($value) && ($value === 'AND' || $value === 'OR')) {
                        if ($i > 0) {
                            $where .= " $value ";
                        }
                    } elseif (is_array($value)) {
                        $where .= "(";

                        $whereTable = $value['whereTable'] ?? $value[0];
                        $whereTableColumnName = $value['whereTableColumnName'] ?? $value[1];
                        $whereOperator = $value['whereOperator'] ?? $value[2];
                        $whereValue = $value['whereValue'] ?? $value[3] ?? '';

                        $where .= self::whereClauseQuery($whereTable, $whereTableColumnName, $whereOperator, $whereValue, $tableAbbreviationArray);
                        $where .= ")";
                    }
                    $i++;
                }
            }
        }else {
            $where .= "(";

            $whereTable = $whereArray['whereTable'] ?? $whereArray[0];
            $whereTableColumnName = $whereArray['whereTableColumnName'] ?? $whereArray[1];
            $whereOperator = $whereArray['whereOperator'] ?? $whereArray[2];
            $whereValue = $whereArray['whereValue'] ?? $whereArray[3] ?? '';

            $where .= self::whereClauseQuery($whereTable, $whereTableColumnName, $whereOperator, $whereValue, $tableAbbreviationArray);
            $where .= ")";
        }

        return $where;
    }

    protected static function whereClauseQuery($whereTable, $whereTableColumnName, $whereOperator, $whereValue, $tableAbbreviationArray)
    {
        $where = "";

        if($whereTable === "**SUB**"){
            $tableAbbreviation = self::findTableAbbreviation($whereTable, $tableAbbreviationArray, 'whereTable');
        }else {
            $tableAbbreviation = self::findTableAbbreviation($whereTable, $tableAbbreviationArray, 'whereTable');
        }

        $addQuotesAroundValue = self::includeQuotes($whereOperator, $whereValue);

        $where .= $tableAbbreviation;
        $where .= ".";
        $where .= $whereTableColumnName;
        $where .= " ";
        $where .= $whereOperator;
        if(!empty($whereValue)) {
            $where .= " ";
            $where .= $addQuotesAroundValue ? "'" . $whereValue . "'" : $whereValue;
        }

        return $where;
    }

    protected static function includeQuotes($whereOperator, $whereValue){
        if (
            (
                !is_numeric($whereValue) &&
                !self::regexPresent('/(NOT| IN )/', $whereOperator)
            ) &&
            !self::regexPresent('/(:|\?)/', $whereValue)
        ){
            return true;
        }
        return false;
    }

    protected static function regexPresent($regex, $whereString){
        if(preg_match($regex, $whereString)){
            return true;
        }
        return false;
    }

    protected static function groupByQuery($groupByArray, $tableAbbreviationArray = [])
    {
        $i = 0;
        $groupBy = " GROUP BY ";
        if(isset($groupByArray[0]) && is_array($groupByArray[0])){
            $last = end($groupByArray);
            foreach($groupByArray as $key => $value){
                $groupByTable = $groupByArray[$i]['groupByTable'] ?? $groupByArray[$i][0];
                $groupByColumn = $groupByArray[$i]['groupByColumn'] ?? $groupByArray[$i][1];
                $groupByOrder = $groupByArray[$i]['groupByOrder'] ?? $groupByArray[$i][2] ?? '';

                $tableAbbreviations = self::findTableAbbreviation($groupByTable, $tableAbbreviationArray, 'groupByTable');
                $groupByTableAbbreviation = $tableAbbreviations[0];

                $groupBy .= self::groupByStatement($groupByTableAbbreviation, $groupByColumn, $groupByOrder);

                $groupBy .= self::commaInQuery($i, $groupByArray, $value, $last);
                $i++;
            }
        }else{
            $groupByTable = $groupByArray['groupByTable'] ?? $groupByArray[0];
            $groupByColumn = $groupByArray['groupByColumn'] ?? $groupByArray[1];
            $groupByOrder = $groupByArray['groupByOrder'] ?? $groupByArray[2] ?? '';

            $tableAbbreviations = self::findTableAbbreviation($groupByTable, $tableAbbreviationArray, 'groupByTable');
            $groupByTableAbbreviation = $tableAbbreviations[0];

            $groupBy .= self::groupByStatement($groupByTableAbbreviation, $groupByColumn, $groupByOrder);
        }
        return $groupBy;
    }

    protected static function groupByStatement($groupByTableAbbreviation, $groupByColumn, $groupByOrder = null)
    {
        $group = "";

        $group .= self::nestTableNameInFunction($groupByColumn, $groupByTableAbbreviation);

        if(!empty($groupByOrder)){
            $group .= " ";
            $group .= $groupByOrder;
        }

        return $group;
    }

    protected static function orderByQuery($orderByArray, $tableAbbreviationArray = [])
    {
        $i = 0;
        $orderBy = " ORDER BY ";
        if(isset($orderByArray[0]) && is_array($orderByArray[0])){
            $last = end($orderByArray);
            foreach($orderByArray as $key => $value){
                $orderByTable = $orderByArray[$i]['orderByTable'] ?? $orderByArray[$i][0];
                $orderByColumn = $orderByArray[$i]['orderByColumn'] ?? $orderByArray[$i][1];
                $orderByOrder = $orderByArray[$i]['sortOrder'] ?? $orderByArray[$i][2] ?? '';

                $tableAbbreviations = self::findTableAbbreviation($orderByTable, $tableAbbreviationArray, 'orderByTable');
                $orderByTableAbbreviation = $tableAbbreviations[0];

                $orderBy .= self::groupByStatement($orderByTableAbbreviation, $orderByColumn, $orderByOrder);

                $orderBy .= self::commaInQuery($i, $orderByArray, $value, $last);
                $i++;
            }
        }else{
            $orderByTable = $orderByArray['orderByTable'] ?? $orderByArray[0];
            $orderByColumn = $orderByArray['orderByColumn'] ?? $orderByArray[1];
            $orderByOrder = $orderByArray['sortOrder'] ?? $orderByArray[2] ?? '';

            $tableAbbreviations = self::findTableAbbreviation($orderByTable, $tableAbbreviationArray, 'orderByTable');
            $orderByTableAbbreviation = $tableAbbreviations[0];

            $orderBy .= self::orderByStatement($orderByTableAbbreviation, $orderByColumn, $orderByOrder);
        }
        return $orderBy;
    }

    protected static function orderByStatement($orderByTableAbbreviation, $orderByColumn, $orderByOrder = null)
    {
        $order = "";

        $order .= $orderByTableAbbreviation;
        $order .= ".";
        $order .= $orderByColumn;

        if(!empty($orderByOrder)){
            $order .= " ";
            $order .= $orderByOrder;
        }

        return $order;
    }

    protected static function selectQuery($selectArray, $tableAbbreviationArray = [], $tableAbbreviation = '')
    {
        $queryArray = self::selectMainQuery($selectArray, $tableAbbreviation);

        $select = $queryArray[0];
        $tableAbbreviationArray = $queryArray[1];
        $tableAbbreviation = $queryArray[2];

        $response = [
            'query' => $select,
            'tableAbbreviationArray' => $tableAbbreviationArray,
            'tableAbbreviation' => $tableAbbreviation
        ];
        return $response;
    }

    protected static function selectMainQuery($selectArray, $tableAbbreviation)
    {
        $selectTable = $selectArray['selectTable'] ?? $selectArray[1];
        if(is_array($selectTable)){
            $tableAbbreviation = 'sub';
            $tableAbbreviationArray[$tableAbbreviation] = '**SUB**';
        }else {
            $tableAbbreviationArray[$tableAbbreviation] = $selectTable;
        }
        $select = "SELECT ";

        $columnArray = $selectArray['selectColumns'] ?? $selectArray[0];

        $select .= self::selectColumnArrayQuery($columnArray, $tableAbbreviation);
        $select .= self::selectTableQuery($tableAbbreviation, $selectTable);

        $tableAbbreviation++;

        $queryArray = [
            $select,
            $tableAbbreviationArray,
            $tableAbbreviation
        ];
        return $queryArray;
    }

    protected static function selectColumnQuery($tableAbbreviation, $column, $as = null, $value = null)
    {
        $selectColumn = '';
        $selectColumn .= self::nestTableNameInFunction($column, $tableAbbreviation);
        if($as){
            $selectColumn .= " AS ";
            $selectColumn .= $value;
        }
        return $selectColumn;
    }
    protected static function selectTableQuery($tableAbbreviation, $table)
    {
        $selectTable = "";
        $selectTable .= " FROM ";
        if(is_array($table)){
            $selectTable .= "(";
            $selectTable .= self::arrayToQuery($table[0]);
            $selectTable .= ")";
        }else {
            $selectTable .= "`";
            $selectTable .= $table;
            $selectTable .= "`";
        }
        $selectTable .= " ";
        $selectTable .= $tableAbbreviation;
        $selectTable .= " ";
        return $selectTable;
    }

    protected static function selectColumnArrayQuery($columnArray, $tableAbbreviation)
    {
        $i = 0;
        $select = "";
        if(is_array($columnArray)) {
            $last = end($columnArray);
            $count = count($columnArray);
            foreach ($columnArray as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $column => $as) {
                        $select .= self::selectColumnQuery($tableAbbreviation, $column, true, $as);
                    }
                } else {
                    if(is_numeric($key)){
                        $select .= self::selectColumnQuery($tableAbbreviation, $value);
                    }else {
                        $select .= self::selectColumnQuery($tableAbbreviation, $key, true, $value);
                    }
                }
                $select .= self::commaInQuery($i, $columnArray, $value, $last, $count);
                $i++;
            }
        }else{
            $select .= self::selectColumnQuery($tableAbbreviation, $columnArray);
        }
        return $select;
    }

    protected static function joinQuery($joinArray, $tableAbbreviationArray = [], $tableAbbreviation = '', $query)
    {
        $queryArray = self::joinMainQuery($joinArray, $tableAbbreviationArray, $tableAbbreviation, $query);

        $query = $queryArray[0];
        $tableAbbreviationArray = $queryArray[1];
        $tableAbbreviation = $queryArray[2];

        $response = [
            'query' => $query,
            'tableAbbreviationArray' => $tableAbbreviationArray,
            'tableAbbreviation' => $tableAbbreviation
        ];
        return $response;
    }

    protected static function joinMainQuery($joinArray, $tableAbbreviationArray, $tableAbbreviation, $query)
    {
        $i = 0;
        $join = '';
        $joinColumns = '';
        if (isset($joinArray[0]) && is_array($joinArray[0])){
            $lastKey = count($joinArray)-1;
            foreach ($joinArray as $key => $array) {
                $joinType = $joinArray[$i]['joinType'] ?? $joinArray[$i][0];
                $joinTable = $joinArray[$i]['joinTable'] ?? $joinArray[$i][1];
                $joinTableColumnName = $joinArray[$i]['joinTableColumnName'] ?? $joinArray[$i][2];
                $joinOperator = $joinArray[$i]['joinOperator'] ?? $joinArray[$i][3];
                $joinToTable = $joinArray[$i]['joinToTable'] ?? $joinArray[$i][4];
                $joinToTableColumnName = $joinArray[$i]['joinToTableColumnName'] ?? $joinArray[$i][5];
                $columnArray = $joinArray[$i]['joinColumns'] ?? $joinArray[$i][6] ?? '';

                $tableAbbreviations = self::joinTableAbbreviation($joinTable, $tableAbbreviationArray, $tableAbbreviation);
                $tableAbbreviationArray = $tableAbbreviations[0];
                $joinTableAbbreviation = $tableAbbreviations[1];

                $tableAbbreviations = self::findTableAbbreviation($joinToTable, $tableAbbreviationArray, 'joinToTable');
                $joinToTableAbbreviation = $tableAbbreviations[0];
                if($columnArray) {
                    $joinColumns .= self::joinColumnArrayQuery($columnArray, $joinTableAbbreviation, $key, $lastKey);
                }

                $join .= self::joinQueryJoin($joinType, $joinTable, $joinTableAbbreviation, $joinTableColumnName, $joinOperator, $joinToTableAbbreviation, $joinToTableColumnName);

                $tableAbbreviation++;
                $i++;
            }
        }else{
            $joinType = $joinArray['joinType'] ?? $joinArray[0];
            $joinTable = $joinArray['joinTable'] ?? $joinArray[1];
            $joinTableColumnName = $joinArray['joinTableColumnName'] ?? $joinArray[2];
            $joinOperator = $joinArray['joinOperator'] ?? $joinArray[3];
            $joinToTable = $joinArray['joinToTable'] ?? $joinArray[4];
            $joinToTableColumnName = $joinArray['joinToTableColumnName'] ?? $joinArray[5];
            $columnArray = $joinArray['joinColumns'] ?? $joinArray[6] ?? '';

            $tableAbbreviations = self::joinTableAbbreviation($joinTable, $tableAbbreviationArray, $tableAbbreviation);
            $tableAbbreviationArray = $tableAbbreviations[0];
            $joinTableAbbreviation = $tableAbbreviations[1];

            $tableAbbreviations = self::findTableAbbreviation($joinToTable, $tableAbbreviationArray, 'joinToTable');
            $joinToTableAbbreviation = $tableAbbreviations[0];

            if($columnArray) {
                $joinColumns .= self::joinColumnArrayQuery($columnArray, $joinTableAbbreviation);
            }

            $join .= self::joinQueryJoin($joinType, $joinTable, $joinTableAbbreviation, $joinTableColumnName, $joinOperator, $joinToTableAbbreviation, $joinToTableColumnName);

            $tableAbbreviation++;
        }
        $querySelect = explode(" FROM", $query);

        $query = $querySelect[0];
        if($joinColumns) {
            $query .= "," . $joinColumns . " FROM" . $querySelect[1] . $join;
        }
        $queryArray = [
            $query,
            $tableAbbreviationArray,
            $tableAbbreviation
        ];
        return $queryArray;
    }

    protected static function joinColumnArrayQuery($columnArray, $joinTableAbbreviation, $currentParentKey = null, $lastParentKey = null)
    {
        $j = 0;
        $joinColumns = "";
        if(is_array($columnArray)) {
            $last = end($columnArray);
            $count = count($columnArray);
            foreach ($columnArray as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $column => $as) {
                        $joinColumns .= self::joinColumnsQuery($joinTableAbbreviation, $column, true, $as);
                    }
                } else {
                    $joinColumns .= self::joinColumnsQuery($joinTableAbbreviation, $value);
                }
                $joinColumns .= self::commaInQuery($j, $columnArray, $value, $last, $count);
                $j++;
            }
        }else{
            $joinColumns .= self::joinColumnsQuery($joinTableAbbreviation, $columnArray);
        }
        if($currentParentKey !== $lastParentKey){
            $joinColumns .= ",";
        }
        return $joinColumns;
    }

    protected static function joinColumnsQuery($joinTableAbbreviation, $column, $as = null, $value = null)
    {
        $columns = "";
        $columns .= self::nestTableNameInFunction($column, $joinTableAbbreviation);
//        $columns .= $joinTableAbbreviation . ".";
//        $columns .= $column;
        if($as) {
            $columns .= " AS ";
            $columns .= $value;
        }
        return $columns;
    }

    protected static function joinTableQuery($joinType, $table, $joinTableAbbreviation)
    {
        $joinTable = "";
        $joinTable .= $joinType;
        $joinTable .= " `";
        $joinTable .= $table;
        $joinTable .= "` ";
        $joinTable .= $joinTableAbbreviation;
        $joinTable .= " ON ";
        return $joinTable;
    }

    protected static function joinTableColumnQuery($joinTableAbbreviation, $joinTableColumnName)
    {
        $joinTableColumn = "";
        $joinTableColumn .= $joinTableAbbreviation;
        $joinTableColumn .= ".";
        $joinTableColumn .= $joinTableColumnName;
        $joinTableColumn .= " ";
        return $joinTableColumn;
    }

    protected static function joinOperatorQuery($joinOperator)
    {
        $operator = "";
        $operator .= $joinOperator;
        $operator .= " ";
        return $operator ;
    }

    protected static function joinToTableColumnQuery($joinToTableAbbreviation, $joinToTableColumnName)
    {
        $joinToTableColumn = "";
        $joinToTableColumn .= $joinToTableAbbreviation;
        $joinToTableColumn .= ".";
        $joinToTableColumn .= $joinToTableColumnName;
        $joinToTableColumn .= " ";
        return $joinToTableColumn;
    }

    protected static function joinTableAbbreviation($joinTable, $tableAbbreviationArray, $tableAbbreviation)
    {
        if (!in_array($joinTable, $tableAbbreviationArray)) {
            $tableAbbreviationArray[$tableAbbreviation] = $joinTable;
            $joinTableAbbreviation = $tableAbbreviation;
            $array = [
                $tableAbbreviationArray,
                $joinTableAbbreviation
            ];
        } else {
            throw new \Exception("The 'joinTable' is missing in your join statement. Please add the 'joinTable' key/value pair in your query and try again.");
        }
        return $array;
    }

    protected static function findTableAbbreviation($findTable, $tableAbbreviationArray, $tableName)
    {
        if (in_array($findTable, $tableAbbreviationArray)) {
            $tableAbbreviation = array_search($findTable, $tableAbbreviationArray);
        } else {
            throw new \Exception("The table '$findTable' is missing in your query. Please add the '$tableName' key/value pair in your query and try again.");
        }
        return $tableAbbreviation;
    }

    protected static function joinQueryJoin($joinType, $joinTable, $joinTableAbbreviation, $joinTableColumnName, $joinOperator, $joinToTableAbbreviation, $joinToTableColumnName)
    {
        $join = '';

        //JoinType, Join Table & Abbreviation
        $join .= self::joinTableQuery($joinType, $joinTable, $joinTableAbbreviation);

        //Concatenate Join Table to Join Column
        $join .= self::joinTableColumnQuery($joinTableAbbreviation, $joinTableColumnName);

        //Operator to compare Join Column with JoinTo Column
        $join .= self::joinOperatorQuery($joinOperator);

        //Concatenate JoinTo Table to JoinTo Column
        $join .= self::joinToTableColumnQuery($joinToTableAbbreviation, $joinToTableColumnName);
        return $join;
    }

    protected static function insertQuery($insertArray)
    {

    }

    protected static function updateQuery($updateArray)
    {

    }

    protected static function deleteQuery($deleteArray)
    {

    }

    protected static function commaInQuery($j, $array, $value = null, $lastValue = null, $count = null)
    {
        $query = '';

        if($value){
            if ((count($array) > 1) && $value !== $lastValue) {
                $query .= ",";
            }
        }else {
            if (($j == 0 && count($array) > 1)) {
                $query .= ",";
            }
        }

        return $query;
    }

    protected static function nestTableNameInFunction($tableColumn, $tableAbbreviation){
        $column = '';
        if(strpos($tableColumn, '(') !== false){
            $columns = explode('(', strrev($tableColumn), 2);
            $firstColumn = strrev($columns[0]);
            $secondColumn = strrev($columns[1]);
            $column .= $secondColumn . "(" . $tableAbbreviation . "." . $firstColumn;
        }else {
            $column .= $tableAbbreviation;
            $column .= ".";
            $column .= $tableColumn;
        }
        return $column;
    }
}