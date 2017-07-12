<?php

namespace models\channels;

use models\ModelDB as MDB;

class Tracking
{

    public static function getUnshippedOrdersByChannel($channel)
    {

        $sql = "SELECT a.order_num, a.type, a.processed, c.item_id 
                FROM order_sync a
                JOIN sync.order b ON b.order_num = a.order_num
                JOIN order_item c ON c.order_id = b.id
                WHERE a.track_successful IS NULL 
                AND a.type = :channel
                AND b.cancelled IS NULL 
                ORDER BY a.processed";
        $queryParams = [
            ':channel' => $channel
        ];
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function getUnshippedOrders()
    {
        $sql = "SELECT a.order_num, a.type, a.processed, c.item_id 
                FROM order_sync a
                JOIN sync.order b ON b.order_num = a.order_num
                JOIN order_item c ON c.order_id = b.id
                WHERE a.track_successful IS NULL 
                AND b.cancelled IS NULL
                ORDER BY a.processed";
        return MDB::query($sql, [], 'fetchAll');
    }

    public static function findUnshippedOrders($channel = null)
    {
        if (!empty($channel)) {
            return Tracking::getUnshippedOrdersByChannel($channel);
        }
        return Tracking::getUnshippedOrders();
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

    public static function getId($orderID, $trackingNumber)
    {
        $sql = "SELECT id 
                FROM tracking 
                WHERE order_id = :order_id 
                AND tracking_num = :tracking_num";
        $queryParams = [
            ':order_id' => $orderID,
            ':tracking_num' => $trackingNumber
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save($orderID, $trackingNumber, $carrier)
    {
        $sql = "INSERT INTO tracking (order_id, tracking_num, carrier) 
                VALUES (:order_id, :tracking_num, :carrier)";
        $queryParams = [
            ':order_id' => $orderID,
            ':tracking_num' => $trackingNumber,
            ':carrier' => $carrier
        ];
        return MDB::query($sql, $queryParams, 'id');
    }

    public static function updateTrackingNum($orderID, $trackingNumber, $carrier)
    {
        $id = Tracking::getId($orderID, $trackingNumber);
        if (empty($id)) {
            $id = Tracking::save($orderID, $trackingNumber, $carrier);
        }
        return $id;
    }
}