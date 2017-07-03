<?php

namespace controllers\channels;

class ChannelHelperController
{
    //To sanitize column names before inserted into query directly
    public static function sanitize_table_name($tab)
    {
        $table = '';
        $tableArray = [
            'listing_amazon',
            'listing_bigcommerce',
            'listing_ebay',
            'listing_ecd',
            'listing_reverb',
            'listing_wc',
            'categories_ebay',
            'categories_amazon',
            'categories_bigcommerce',
            'categories_amazon_id',
            'categories_ebay_id',
            'categories_bigcommerce_id',
            'categories_reverb_id',
        ];
        if (in_array($tab, $tableArray)) {
            $table = $tab;
        }
        return $table;
    }

    public static function sanitize_time_period($period)
    {
        $interval = '';
        $periodArray = [
            'MONTH',
            'WEEK',
            'YEAR',
            'DAY',
        ];
        if (in_array($period, $periodArray)) {
            $interval = $period;
        }
        return $interval;
    }

    public static function determine_time_condition($dateColumn, $period, $period2 = null, $period3 = null)
    {
        $condition = '';
        if (empty($period2)) {
            switch ($period) {
                case $period === 'TODAY':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND MONTH($dateColumn) = MONTH(NOW()) AND DAY($dateColumn) = DAY(NOW())";
                    break;
                case $period === 'YESTERDAY':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND MONTH($dateColumn) = MONTH(NOW()) AND DAY($dateColumn) = DAY(NOW() - INTERVAL 1 DAY)";
                    break;
                case $period === 'THISWTD':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND WEEKOFYEAR($dateColumn) = WEEKOFYEAR(NOW())";
                    break;
                case $period === 'THISMTD':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND MONTH($dateColumn) = MONTH(NOW())";
                    break;
                case $period === 'MTDLASTYEAR':
                    $condition = "YEAR($dateColumn) = YEAR(NOW() - INTERVAL 1 YEAR) AND MONTH($dateColumn) = MONTH(NOW()) AND DAY($dateColumn) <= DAY(NOW())";
                    break;
                case $period === 'LASTYEARMONTH':
                    $condition = "YEAR($dateColumn) = YEAR(NOW() - INTERVAL 1 YEAR) AND MONTH($dateColumn) = MONTH(NOW())";
                    break;
                case $period === 'LASTMTD':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND MONTH($dateColumn) = MONTH(NOW() - INTERVAL 1 MONTH) AND DAY($dateColumn) <= DAY(NOW())";
                    break;
                case $period === 'LASTMONTH':
                    $condition = "YEAR($dateColumn) = YEAR(NOW()) AND MONTH($dateColumn) = MONTH(NOW() - INTERVAL 1 MONTH)";
                    break;
                case $period === 'THISYTD':
                    $condition = "YEAR($dateColumn) = YEAR(NOW())";
                    break;
                case $period === 'LASTYTD':
                    $condition = "YEAR($dateColumn) = YEAR(NOW() - INTERVAL 1 YEAR) AND MONTH($dateColumn) <= MONTH(NOW()) AND DAY($dateColumn) <= DAY(NOW())";
                    break;
                case $period === 'LASTYEAR':
                    $condition = "YEAR($dateColumn) = YEAR(NOW() - INTERVAL 1 YEAR)";
                    break;
            }
        } else {
            if (empty($period3)) {
                $period3 = date('Y-m-d');
            } else {
                $period3 = date_create($period3);
                $period3 = $period3->format('Y-m-d');
            }
            $period2 = date_create($period2);
            $period2 = $period2->format('Y-m-d');
            $condition = "date BETWEEN $period2 AND $period3";
        }
        return $condition;
    }

    //Parse array for select statement
    public static function parseConditions($array)
    {
        $return_array = [];
        $select_parameters = '';
        $query_param = [];
        $count = count($array);
        $i = 0;
        foreach ($array as $key => $value) {
            if ($key == 'date') {
                $select_parameters .= "$key BETWEEN '$value 00:00:00' AND '$value 23:59:59'";
            } else {
                $column = $key;
                if(strpos($key, '.') !== false){
                    $key = substr(strstr($key, '.'),1);
                }
                $select_parameters .= "$column LIKE :$key";
                $query_param[$key] = "%" . $value . "%";
            }
            if (++$i !== $count) {
                $select_parameters .= " AND ";
            }

        }
        $return_array[0] = $select_parameters;
        $return_array[1] = $query_param;
        return $return_array;
    }

    public static function addToArray($parentArray, $arrayToAdd)
    {
        if (count($arrayToAdd) === count($arrayToAdd, COUNT_RECURSIVE)) {
            $newArray = array_merge(
                $parentArray,
                [
                    $arrayToAdd
                ]
            );
        } else {
            $newArray = array_merge(
                $parentArray, $arrayToAdd
            );
        }
        return $newArray;
    }

}