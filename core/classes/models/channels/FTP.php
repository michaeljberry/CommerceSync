<?php

namespace models\channels;


class FTP
{

    public static function saveFileToDisk($folder, $filename, $orderXml)
    {
        file_put_contents($folder . $filename, $orderXml);
        chmod($folder . $filename, 0777);
        file_put_contents($folder . 'backup/' . $filename, $orderXml);
        chmod($folder . 'backup/' . $filename, 0777);
    }

    public static function saveXmlToFTP($orderNum, $orderXml, $folder, $channel)
    {
        $filename = $orderNum . '.xml';
        echo $filename . '<br />';
        FTP::saveFileToDisk($folder, $filename, $orderXml);
        if (file_exists($folder . $filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = Order::saveToSync($orderNum, 1, $channel);
            if ($results) {
                echo "$orderNum successfully updated in DB.";
            }
        }
    }
}