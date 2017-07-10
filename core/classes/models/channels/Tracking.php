<?php

namespace models\channels;

use models\ModelDB as MDB;

class Tracking
{
    public static function getUnshippedOrders($channel = null)
    {
        $params = [];
        if (!empty($channel)) {
            $params = [
                ':channel' => $channel
            ];
        }
        $sql = "SELECT a.order_num, a.type, a.processed, c.item_id 
                FROM order_sync a
                JOIN sync.order b ON b.order_num = a.order_num
                JOIN order_item c ON c.order_id = b.id
                WHERE a.track_successful IS NULL";
        $sql .= !empty($channel) ? " AND a.type = :channel" : "";
        $sql .= " AND b.cancelled IS NULL
                ORDER BY a.processed";
        return MDB::query($sql, $params, 'fetchAll');
    }

    public static function updateTrackingSuccessful($order_num)
    {
        $sql = "UPDATE order_sync 
                SET track_successful = '1' 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $order_num
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateTrackingNum($order_id, $tracking_num, $carrier)
    {
        $sql = "SELECT id 
                FROM tracking 
                WHERE order_id = :order_id 
                AND tracking_num = :tracking_num";
        $queryParams = [
            ':order_id' => $order_id,
            ':tracking_num' => $tracking_num
        ];
        $tracking_id = MDB::query($sql, $queryParams, 'fetchColumn');
        if (empty($tracking_id)) {
            $sql = "INSERT INTO tracking (order_id, tracking_num, carrier) 
                    VALUES (:order_id, :tracking_num, :carrier)";
            $queryParams = [
                ':order_id' => $order_id,
                ':tracking_num' => $tracking_num,
                ':carrier' => $carrier
            ];
            $tracking_id = MDB::query($sql, $queryParams, 'id');
        }
        return $tracking_id;
    }
}