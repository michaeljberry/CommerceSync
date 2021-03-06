<?php

namespace controllers\channels;


use models\channels\order\Order;

class FTPController
{

    public static function saveXml(Order $order)
    {
        $filename = $order->getOrderNumber() . '.xml';
        echo $filename . '<br />';
        FTPController::saveToDisk($filename, $order->getOrderXml());
        if (file_exists(getenv("FTP_FOLDER") . '/' . $filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = Order::saveToSync($order->getOrderNumber(), 1, $order->getChannelName());
            if ($results) {
                echo "{$order->getOrderNumber()} successfully updated in DB.";
            }
        }
    }

    public static function saveToDisk($filename, $orderXML)
    {
        file_put_contents(getenv("FTP_FOLDER") . '/' . $filename, $orderXML);
        chmod(getenv("FTP_FOLDER") . '/' . $filename, 0777);
        if(file_exists(getenv("FTP_FOLDER") . '/' . $filename))
        {
            file_put_contents(getenv("FTP_FOLDER") . '/' . 'backup/' . $filename, $orderXML);
            chmod(getenv("FTP_FOLDER") . '/' . 'backup/' . $filename, 0777);
        } else {
            FTPController::saveToDisk($filename, $orderXML);
        }
    }
}
