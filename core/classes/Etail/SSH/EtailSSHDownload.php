<?php

namespace Etail\SSH;

use Exception;
use Etail\Etail;
use controllers\channels\FTPController;

class EtailSSHDownload extends Etail
{
    protected $fileLocation;
    protected $fileLocationPath;
    protected $fileStream;
    protected $filePath;
    protected $ftp;

    public function __construct()
    {
        parent::__construct();

        $this->ftp = new FTPController();
    }

    public function download($filePath)
    {
        $destinationFilePath = $this->ftp->getFtpFolder();
        $this->filePath = $this->getEtailRootFolder() . "/" . $filePath;
        $this->fileStream = new EtailSSHStream($this->filePath, $directory = true);

        $this->downloadFiles($this->fileStream);
    }

    protected function getAllOrders($stream)
    {
        while ($this->fileStream && ($fileName = readdir($stream->getStream())) !== false) {
            if (in_array($fileName, $this->getFoldersToIgnore())) {
                continue;
            }

            $fileContents = file_get_contents(
                "ssh2.sftp://" .
                intval($stream->getSFTP()) .
                "/" .
                $this->filePath .
                "/" .
                $fileName
            );
            $this->writeOrdersForVAI($fileName, $fileContents);
        }

    }

    protected function writeOrdersForVAI($fileName, $fileContents)
    {
        $this->ftp->saveToDisk($fileName, $fileContents);
    }

    protected function downloadFiles(EtailSSHStream $stream)
    {
        $this->getAllOrders($stream);
        $stream->close($directory = true);
    }

    public function getFoldersToIgnore()
    {
        return $this->foldersToIgnore;
    }
}
