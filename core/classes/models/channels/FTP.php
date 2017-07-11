<?php

namespace models\channels;


class FTP
{

    public static function saveXml($orderNum, $orderXML, $folder, $channel)
    {
        $filename = $orderNum . '.xml';
        echo $filename . '<br />';
        FTP::saveToDisk($folder, $filename, $orderXML);
        if (file_exists($folder . $filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = Order::saveToSync($orderNum, 1, $channel);
            if ($results) {
                echo "$orderNum successfully updated in DB.";
            }
        }
    }

    public static function saveToDisk($folder, $filename, $orderXML)
    {
        file_put_contents($folder . $filename, $orderXML);
        chmod($folder . $filename, 0777);
        file_put_contents($folder . 'backup/' . $filename, $orderXML);
        chmod($folder . 'backup/' . $filename, 0777);
    }
}