<?php

namespace controllers\channels;


use models\channels\order\Order;

class FTPController
{

    protected $ftpFolder;

    public function __construct()
    {
        $this->ftpFolder = getenv('ETAIL_FTP_DIRECTORY');
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

    public function saveToDisk($filename, $fileContents)
    {
        $this->saveFile($filename, $fileContents);
        $this->updateFilePermissions($filename);

        if($this->fileExists($filename)) {
            $backup = true;
            $this->saveFile($filename, $fileContents, $backup);
            $this->updateFilePermissions($filename, $backup);
            return true;
        }

        $this->saveToDisk($filename, $fileContents);
    }
    public function getFtpFolder()
    {
        return $this->ftpFolder;
    }

    public function fileExists($fileName)
    {
        return file_exists("{$this->getFtpFolder()}/{$fileName}");
    }

    protected function saveFile($fileName, $fileContents, $backup = false)
    {
        $fileLocation = $this->getFileLocation($fileName, $backup);

        file_put_contents($fileLocation, $fileContents);
    }

    protected function updateFilePermissions($fileName, $backup = false, $permissions = 0777)
    {
        $fileLocation = $this->getFileLocation($fileName, $backup);

        chmod($fileLocation, $permissions);
    }

    protected function getFileLocation($fileName, $backup)
    {
        $fileLocation = $this->getFtpFolder() . DIRECTORY_SEPARATOR;

        if ($backup) $fileLocation .= "backup" . DIRECTORY_SEPARATOR;

        $fileLocation .= $fileName;

        return $fileLocation;
    }
}
