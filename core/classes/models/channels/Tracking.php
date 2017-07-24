<?php

namespace models\channels;

use models\ModelDB as MDB;

class Tracking
{

    private $tracker = [];

    public function setChannelName($channelName)
    {
        if(!isset($this->tracker[$channelName])){
//            $this->setChannelNumbers($channelName);
            $channelTracking = $channelName . "\\" . $channelName . "Tracking";
            $this->tracker[$channelName] = new $channelTracking($channelName);
        }
    }

    public function setOrderTracking($channelName, $orderNumber, $trackingNumber, $carrier)
    {
//        $this->tracker[$channelName]['orders'][$orderNumber]['trackingNumber'] = $trackingNumber;
//        $this->tracker[$channelName]['orders'][$orderNumber]['carrier'] = $carrier;
        $channelOrderTracking = $channelName . "\\" . $channelName . "OrderTracking";
        $this->getChannel($channelName)->updateOrders(new $channelOrderTracking($orderNumber, $trackingNumber, $carrier));
    }

    public function setShipped($channelName, $orderNumber)
    {
        $this->getOrder($channelName, $orderNumber)->setShipped();
    }

    public function setSuccess($channelName, $orderNumber)
    {
        $this->getOrder($channelName, $orderNumber)->setSuccess();
    }

    public function getChannel($channelName)
    {
        return $this->tracker[$channelName];
    }

    public function getOrder($channelName, $orderNumber)
    {
        return $this->getChannel($channelName)->getOrder($orderNumber);
    }

    public function setItemTransactionId($channelName, $orderNumber, $itemID, $transactionID)
    {
        $this->getOrder($channelName, $orderNumber)->setItemId($itemID);
        $this->getOrder($channelName, $orderNumber)->setTransactionId($transactionID);
    }

    public function getChannelNumbers($channelName)
    {
        return $this->tracker[$channelName]->getChannelNumbers();
    }

    public function getTrackingNumber($channelName, $orderNumber)
    {
        return $this->getOrder($channelName, $orderNumber)->getTrackingNumber();
    }

    public static function getUnshippedOrdersByChannel($channelName)
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
            ':channel' => $channelName
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

    public static function findUnshippedOrders($channelName = null)
    {
        if (!empty($channelName)) {
            return Tracking::getUnshippedOrdersByChannel($channelName);
        }
        return Tracking::getUnshippedOrders();
    }

    public static function updateTrackingSuccessful($orderNumber)
    {
        $sql = "UPDATE order_sync 
                SET track_successful = '1' 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $orderNumber
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

    public static function updateTrackingNumber($orderID, $trackingNumber, $carrier)
    {
        $id = Tracking::getId($orderID, $trackingNumber);
        if (empty($id)) {
            $id = Tracking::save($orderID, $trackingNumber, $carrier);
        }
        return $id;
    }
}