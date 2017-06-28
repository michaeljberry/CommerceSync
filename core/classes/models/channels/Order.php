<?php

namespace models\channels;

use models\ModelDB as MDB;

class Order
{
    public static function cancel($id)
    {
        $sql = "UPDATE sync.order SET cancelled = 1 WHERE order_num = :order_num";
        $query_params = [
            ':order_num' => $id
        ];
        MDB::query($sql, $query_params);
    }

    public static function getID($order_num)
    {
        $sql = "SELECT id 
                FROM sync.order 
                WHERE order_num = :order_num";
        $query_params = [
            ':order_num' => $order_num
        ];
        return MDB::query($sql, $query_params, 'fetchColumn');
    }

    public static function getBySearch($array, $channel)
    {
        $result_array = CHC::parseConditions($array);
        $condition = $result_array[0];
        $query_params = $result_array[1];
        $query_params['channel'] = $channel;
        $sql = "SELECT o.id, o.order_num, o.date, c.first_name, c.last_name, t.tracking_num, t.carrier 
                FROM sync.order o 
                JOIN customer c ON o.cust_id = c.id 
                LEFT JOIN tracking t ON o.id = t.order_id 
                JOIN order_sync os ON o.order_num = os.order_num 
                WHERE $condition AND os.type = :channel";
        return MDB::query($sql, $query_params, 'fetchAll');
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
        $query_params = [
            ':order_id' => $order_id
        ];
        return MDB::query($sql, $query_params, 'fetch');
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
}