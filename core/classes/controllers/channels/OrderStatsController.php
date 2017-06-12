<?php

namespace controllers\channels;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as EDB;

class OrderStatsController
{
    public static function getOrderStats($channel = null, $time = 2, $period = 'MONTH')
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
        if(empty($channel)){
            $sql .= ", channel";
        }else{
            $params[':channel'] = $channel;
        }
        $results = EDB::query($sql, $params, 'fetchAll');
        return $results;
    }
}