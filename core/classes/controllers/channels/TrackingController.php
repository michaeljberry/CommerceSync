<?php

namespace controllers\channels;

use models\ModelDB as EDB;

class TrackingController
{
    //Get orders that haven't been marked as "tracking_successful"
    public static function getUnshippedOrders($channel = null)
    {
        $params = [];
        if(!empty($channel)){
            $params = [
                ':channel' => $channel
            ];
        }
        $sql = "SELECT a.order_id, a.type, a.processed, c.item_id 
                FROM order_sync a
                JOIN sync.order b ON b.order_num = a.order_id
                JOIN order_item c ON c.order_id = b.id
                WHERE a.track_successful IS NULL";
        $sql .= !empty($channel) ? " AND a.type = :channel" : "";
        $sql .= " AND b.cancelled IS NULL
                ORDER BY a.processed";
        return EDB::query($sql, $params, 'fetchAll');
    }
}