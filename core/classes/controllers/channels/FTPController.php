<?php

namespace controllers\channels;


use models\channels\order\Order;

class FTPController
{

    protected $ftpFolder;

    public function __construct()
    {
        $this->ftpFolder = getenv('FTP_FOLDER');
    }

    public static function saveXml(Order $order)
    {
        $filename = $order->getOrderNumber() . '.xml';
        echo $filename . '<br />';
        FTPController::saveToDisk($filename, $order->getOrderXml());
        if (FTPController::fileExists($filename)) {
            echo "Successfully uploaded $filename<br />";
            $results = Order::saveToSync($order->getOrderNumber(), 1, $order->getChannelName());
            if ($results) {
                echo "{$order->getOrderNumber()} successfully updated in DB.";
            }
        }
    }

    public static function saveToDisk($filename, $orderXML)
    {
        FTPController::saveFile($filename, $orderXML);
        FTPController::updateFilePermissions($filename);

        if(FTPController::fileExists($filename)) {
            $backup = true;
            FTPController::saveFile($filename, $orderXML, $backup);
            FTPController::updateFilePermissions($filename, $backup);
            return true;
        }

        FTPController::saveToDisk($filename, $orderXML);
    }
    public function getFtpFolder()
    {
        return $this->ftpFolder;
    }

    public static function fileExists($fileName)
    {
        return file_exists("{$this->getFtpFolder()}/{$fileName}");
    }

    protected static function saveFile($fileName, $fileContents, $backup = false)
    {
        if ($backup) {
            return file_put_contents("{$this->getFtpFolder()}/backup/{$fileName}", $fileContents);
        }

        file_put_contents("{$this->getFtpFolder()}/{$fileName}", $fileContents);
    }

    protected static function updateFilePermissions($fileName, $backup = false, $permissions = 0777)
    {
        if ($backup) {
            return chmod("{$this->getFtpFolder()}/backup/{$fileName}", $permissions);
        }

        chmod("{$this->getFtpFolder()}/{$fileName}",$permissions);
    }
}
