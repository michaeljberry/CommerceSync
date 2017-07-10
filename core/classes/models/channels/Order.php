<?php

namespace models\channels;

use controllers\channels\ChannelHelperController as CHC;
use ecommerce\Ecommerce;
use models\ModelDB as MDB;

class Order
{
    public static function cancel($id)
    {
        $sql = "UPDATE sync.order 
                SET cancelled = 1 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $id
        ];
        MDB::query($sql, $queryParams);
    }

    public static function getIdByOrderNum($orderNum)
    {
        $sql = "SELECT id 
                FROM sync.order 
                WHERE order_num = :order_num";
        $queryParams = [
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function getBySearch($array, $channel)
    {
        $result_array = CHC::parseConditions($array);
        $condition = $result_array[0];
        $queryParams = $result_array[1];
        $queryParams['channel'] = $channel;
        $sql = "SELECT o.id, o.order_num, o.date, c.first_name, c.last_name, t.tracking_num, t.carrier 
                FROM sync.order o 
                JOIN customer c ON o.cust_id = c.id 
                LEFT JOIN tracking t ON o.id = t.order_id 
                JOIN order_sync os ON o.order_num = os.order_num 
                WHERE $condition 
                AND os.type = :channel";
        return MDB::query($sql, $queryParams, 'fetchAll');
    }

    public static function getByID($order_id)
    {
        $sql = "SELECT o.order_num, o.date, o.ship_method, o.shipping_amount, o.taxes, c.first_name, c.last_name, c.street_address, c.street_address2, city.name AS city, s.name, s.abbr as state_abbr, z.zip, t.tracking_num, t.carrier, os.processed as date_processed, os.success, os.type as channel, os.track_successful 
                FROM sync.order o 
                JOIN customer c ON o.cust_id = c.id 
                LEFT JOIN tracking t ON o.id = t.order_id 
                JOIN order_sync os ON o.order_num = os.order_num 
                JOIN state s ON c.state_id = s.id 
                JOIN city ON c.city_id = city.id 
                JOIN zip z ON c.zip_id = z.id 
                WHERE o.id = :order_id";
        $queryParams = [
            ':order_id' => $order_id
        ];
        return MDB::query($sql, $queryParams, 'fetch');
    }

    public static function markAsShipped($order_num, $channel)
    {
        $response = Tracking::updateTrackingSuccessful($order_num);
        if ($response) {
            echo "Tracking for $channel order $order_num was updated!";
            return true;
        }
        return false;
    }

    public static function updateShippingAmount($order, $shipping_amount)
    {
        $sql = "UPDATE sync.order 
                SET shipping_amount = :shipping_amount 
                WHERE order_num = :order_num";
        $queryParams = [
            ':shipping_amount' => $shipping_amount,
            ':order_num' => $order
        ];
        return MDB::query($sql, $queryParams, 'boolean');
    }

    public static function getIdByStoreIdAndOrderNum($storeID, $orderNum)
    {
        $sql = "SELECT id FROM sync.order WHERE store_id = :store_id AND order_num = :order_num";
        $queryParams = [
            ':store_id' => $storeID,
            ':order_num' => $orderNum
        ];
        return MDB::query($sql, $queryParams, 'fetchColumn');
    }

    public static function save(
        $storeID,
        $custID,
        $orderNum,
        $shipMethod,
        $shippingAmount,
        $taxAmount = 0,
        $fee = 0,
        $channelOrderID = null
    ) {
        $order_id = Order::getIdByStoreIdAndOrderNum($storeID, $orderNum);
        if (empty($order_id)) {
            $sql = "INSERT INTO sync.order (store_id, cust_id, order_num, ship_method, shipping_amount, taxes, fee, channel_order_id) 
                    VALUES (:store_id, :cust_id, :order_num, :ship_method, :shipping_amount, :taxes, :fee, :channel_order_id)";
            $queryParams = [
                ":store_id" => $storeID,
                ":cust_id" => $custID,
                ":order_num" => $orderNum,
                ":ship_method" => $shipMethod,
                ":shipping_amount" => $shippingAmount,
                ":taxes" => $taxAmount,
                ':fee' => $fee,
                ':channel_order_id' => $channelOrderID
            ];
            $order_id = MDB::query($sql, $queryParams, 'id');
        }
        return $order_id;
    }

    public static function save_taxes($orderID, $taxes)
    {
        $sql = "UPDATE sync.order 
                SET taxes = :taxes 
                WHERE id = :id";
        $queryParams = [
            ":taxes" => $taxes,
            ":id" => $orderID
        ];
        return MDB::query($sql, $queryParams, 'id');
    }
}