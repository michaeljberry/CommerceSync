<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;

class OrderStats
{
    public static function get($channel = null, $time = 2, $period = 'MONTH')
    {
        $interval = CHC::sanitize_time_period($period);
        $params = [
            ':time_period' => $time
        ];
        $sql = "SELECT channel, stats_date, sales 
                FROM order_stats
                WHERE stats_date BETWEEN NOW() - INTERVAL :time_period $interval AND NOW()";
        $sql .= !empty($channel) ? "AND channel = :channel" : "";
        $sql .= " GROUP BY DATE(stats_date)";
        if (empty($channel)) {
            $sql .= ", channel";
        } else {
            $params[':channel'] = $channel;
        }
        $results = MDB::query($sql, $params, 'fetchAll');
        return $results;
    }

    public static function save($channel, $date, $sales, $units_sold)
    {
        $sql = "INSERT INTO order_stats (channel, stats_date, sales, units_sold) 
                VALUES (:channel, :date, :sales, :units_sold) 
                ON DUPLICATE KEY UPDATE sales = :sales2, units_sold = :units_sold2";
        $query_params = [
            ':channel' => $channel,
            ':date' => $date,
            ':sales' => $sales,
            ':units_sold' => $units_sold,
            ':sales2' => $sales,
            ':units_sold2' => $units_sold
        ];
        return MDB::query($sql, $query_params, 'boolean');
    }

    public static function getWeek()
    {
        $sql = "SELECT DATE(o.date) AS date, ROUND(SUM(oi.price), 2) AS sales, SUM(oi.quantity) AS units_sold, os.type AS channel 
                FROM sync.order_item oi 
                JOIN sync.order o ON o.id = oi.order_id 
                JOIN order_sync os ON os.order_num = o.order_num 
                WHERE o.date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND NOW() 
                GROUP BY DATE(o.date), os.type";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function getSum($channel = null, $period = 'THISMTD', $period2 = null, $period3 = null)
    {
        $interval = CHC::sanitize_time_period($period);
        $dateColumn = 'stats_date';
        $condition = CHC::determine_time_condition($dateColumn, $period, $period2, $period3);

        if (empty($channel)) {
            $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold FROM order_stats WHERE $condition GROUP BY channel";
            $query_params = [];
        } else {
            $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold FROM order_stats WHERE $condition AND channel = :channel";
            $query_params = [
                ':channel' => $channel
            ];
        }
        return MDB::query($sql, $query_params, 'fetchAll');
    }


}