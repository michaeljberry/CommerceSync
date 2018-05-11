<?php

namespace Etail;

use CSV\CSV;
use Etail\SSH\EtailSSHUpload;

class Etail
{

    protected $csvFile;
    protected $datedFileName;
    protected $localDirectory;

    public function __construct()
    {

        $this->setDatedFileName();

        $this->setLocalDirectory();

    }

    protected function setDatedFileName()
    {

        $this->datedFileName = date('Y-m-d-H-i');

    }

    protected function setLocalDirectory()
    {

        $this->localDirectory = getenv('ETAIL_FTP_DIRECTORY');

    }

    protected function uploadInventoryToSSH($currentFileLocation, $fileDestination)
    {

        return new EtailSSHUpload($currentFileLocation, $fileDestination);

    }

    public function getDatedFileName()
    {

        return $this->datedFileName;

    }

    public function getLocalDirectory()
    {

        return $this->localDirectory;

    }

    protected function uploadCSV()
    {

        return $this->uploadInventoryToSSH($this->csvFile->getFilePath(), $this->getDestinationFolder() . "/" . $this->csvFile->getFileName());

    }

    public function getDestinationFolder()
    {

        return $this->destinationFolder;

    }

    protected function createCSV($csvArray)
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getLocalDirectory(), $csvArray, $this->getCSVHeader());

    }

    public function getCSVHeader()
    {

        return $this->csvHeader;

    }

}