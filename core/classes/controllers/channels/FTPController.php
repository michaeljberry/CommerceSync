<?php

namespace controllers\channels;


use models\channels\order\Order;

class FTPController
{

    public static function saveXml(Order $order)
    {
        $filename = $order->getOrderNum() . '.xml';
        echo $filename . '<br />';
        FTPController::saveToDisk($filename, $order->getOrderXml());
        if (file_exists(FTP_FOLDER . $filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = Order::saveToSync($order->getOrderNum(), 1, $order->getChannelName());
            if ($results) {
                echo "{$order->getOrderNum()} successfully updated in DB.";
            }
        }
    }

    public static function saveToDisk($filename, $orderXML)
    {
        file_put_contents(FTP_FOLDER . $filename, $orderXML);
        chmod(FTP_FOLDER . $filename, 0777);
        file_put_contents(FTP_FOLDER . 'backup/' . $filename, $orderXML);
        chmod(FTP_FOLDER . 'backup/' . $filename, 0777);
    }
}