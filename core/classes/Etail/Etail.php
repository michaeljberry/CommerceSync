<?php

namespace Etail;

use CSV\CSV;
use Etail\SSH\{EtailSSHUpload, EtailSSHDownload};

class Etail
{
    protected $parentFolder = "ChesbroMusic";
    protected $csvFile;
    protected $datedFileName;
    protected $fileFolder;

    public function __construct()
    {
        $this->setFileFolder();
    }

    protected function setFileFolder()
    {
        $this->fileFolder = getenv('ETAIL_FTP_DIRECTORY');
    }

    protected function uploadToSSH($currentFileLocation, $fileDestination)
    {
        return new EtailSSHUpload($currentFileLocation, $fileDestination);
    }

    protected function downloadFromSSH($currentFileLocation, $fileDestination)
    {
        return new EtailSSHDownload($currentFileLocation, $fileDestination);
    }

    public function getFileFolder()
    {
        return $this->fileFolder;
    }

    public function getEtailRootFolder()
    {
        return $this->parentFolder;
    }
}