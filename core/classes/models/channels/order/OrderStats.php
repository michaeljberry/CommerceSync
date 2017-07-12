<?php

namespace models\channels\order;

use controllers\channels\ChannelHelperController as CHC;
use models\ModelDB as MDB;
use PDO;

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
        $group = " GROUP BY DATE(stats_date)";

        if(!empty($channel)){
            $sql .= " AND channel = :channel";
            $params[':channel'] = $channel;
        }else{
            $group .= ", channel";
        }
        $sql .= $group;

        $results = MDB::query($sql, $params, 'fetchAll');
        return $results;
    }

    public static function save($channel, $date, $sales, $unitsSold)
    {
        $sql = "INSERT INTO order_stats (channel, stats_date, sales, units_sold) 
                VALUES (:channel, :date, :sales, :units_sold) 
                ON DUPLICATE KEY UPDATE sales = :sales2, units_sold = :units_sold2";
        $queryParams = [
            ':channel' => $channel,
            ':date' => $date,
            ':sales' => $sales,
            ':units_sold' => $unitsSold,
            ':sales2' => $sales,
            ':units_sold2' => $unitsSold
        ];
        return MDB::query($sql, $queryParams, 'boolean');
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
        $dateColumn = 'stats_date';
        $timeCondition = CHC::determine_time_condition($dateColumn, $period, $period2, $period3);

        if (empty($channel)) {
            list($sql, $queryParams) = OrderStats::getSumTotal($timeCondition);
        } else {
            list($sql, $queryParams) = OrderStats::getSumByChannel($channel, $timeCondition);
        }
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function getSalesBySkuId($skuID)
    {
        $sql = "SELECT
                os.type AS channel,
                (ROUND(SUM(quantity * price), 2) + ROUND(SUM(o.shipping_amount), 2)) as sales,
                SUM(oi.quantity) as unitsSold,
                DATE_FORMAT(date, '%Y-%m') as date
                FROM order_item oi
                LEFT OUTER JOIN `order` o ON o.id = oi.order_id
                LEFT OUTER JOIN order_sync os ON os.order_num = o.order_num
                WHERE oi.sku_id = :sku_id
                GROUP BY channel, DATE_FORMAT(date, '%Y-%m')
                ORDER BY date DESC";
        $queryParams = [
            ':sku_id' => $skuID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function analyzeSales($sku)
    {
        if (empty($sku)) {
            $sql = "SELECT sk.sku, c.name, o.date, oi.price, o.shipping_amount, oi.quantity, p.price AS current_price, o.id 
                    FROM order_item oi 
                    JOIN sync.order o ON o.id = oi.order_id 
                    JOIN store s ON s.id = o.store_id 
                    JOIN channel c ON c.id = s.channel_id 
                    JOIN sku sk ON sk.id = oi.sku_id 
                    JOIN (
                      SELECT p.sku_id, p.price 
                      FROM product_price p 
                      GROUP BY p.sku_id
                      ) p 
                    ON p.sku_id = sk.id 
                    WHERE sk.sku <> '' 
                    AND c.name = 'Ebay' 
                    ORDER BY sk.sku, o.date ASC";
            return MDB::query($sql, [], 'fetchAll');
        }
    }

    /**
     * @param $timeCondition
     * @return array
     */
    public static function getSumTotal($timeCondition): array
    {
        $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold 
                    FROM order_stats 
                    WHERE $timeCondition 
                    GROUP BY channel";
        $queryParams = [];
        return array($sql, $queryParams);
    }

    /**
     * @param $channel
     * @param $timeCondition
     * @return array
     */
    public static function getSumByChannel($channel, $timeCondition): array
    {
        $sql = "SELECT channel, ROUND(SUM(sales), 2) AS sales, SUM(units_sold) AS units_sold 
                    FROM order_stats 
                    WHERE $timeCondition 
                    AND channel = :channel";
        $queryParams = [
            ':channel' => $channel
        ];
        return array($sql, $queryParams);
    }


}