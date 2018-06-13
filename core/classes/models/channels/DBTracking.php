<?php

namespace models\channels;

use PDO;
use models\ModelDB as MDB;
use controllers\channels\ChannelHelperController as CHC;

class DBTracking
{

    public static function updateEtailTracking($valuesArray)
    {

        $values = "";

        $queryParameters = [];
        $sql = "INSERT INTO etail_tracking (order_number,tracking_number,carrier,mail_class,date_shipped,billed_weight,actual_weight,postage_cost) VALUES ";

        foreach ($valuesArray as $key => $valueArray) {

            $values .= "(:order{$key},";

            $values .= ":track{$key},";

            $values .= ":carrier{$key},";

            $values .= ":mail{$key},";

            $values .= ":date{$key},";

            $values .= ":billed{$key},";

            $values .= ":actual{$key},";

            $values .= ":postage{$key}";

            foreach ([$valueArray] as [
                'ORDER_NUM' => $order_number,
                'TRACKING_NUM' => $tracking_number,
                'CARRIER' => $carrier,
                'MAIL_CLASS' => $mail_class,
                'DATE_SHIPPED' => $date,
                'BILLED_WEIGHT' => $billed_weight,
                'ACTUAL_WEIGHT' => $actual_weight,
                'POSTAGE_COST' => $postage_cost
            ]) {
                $queryParameters[":order{$key}"] = trim($order_number);
                $queryParameters[":track{$key}"] = trim($tracking_number);
                $queryParameters[":carrier{$key}"] = trim($carrier);
                $queryParameters[":mail{$key}"] = trim($mail_class);
                $queryParameters[":date{$key}"] = trim($date);
                $queryParameters[":billed{$key}"] = trim($billed_weight);
                $queryParameters[":actual{$key}"] = trim($actual_weight);
                $queryParameters[":postage{$key}"] = trim($postage_cost);
            }

            $values .= $valueArray === end($valuesArray) ? ")" : "),";

        }

        $sql .= $values . " ON DUPLICATE KEY UPDATE order_number = VALUES(order_number)";

        return MDB::query($sql, $queryParameters, 'id');

    }

    public function getTracking($interval = null)
    {

        $sql = "SELECT
            order_number, tracking_number, carrier, mail_class, date_shipped, billed_weight, actual_weight, postage_cost
            FROM etail_tracking tb";

        $sql .= $interval ? " WHERE tb.time_added >= DATE_SUB(NOW(), INTERVAL $interval MINUTE)" : "";

        return MDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);

    }

}