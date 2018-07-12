<?php

namespace Etail\SSH;

use CSV\CSV;

class EtailSSHUploadCSV extends EtailSSHUpload
{
    protected $csvFile;

    public function __construct()
    {
        parent::__construct();
    }

    protected function createCSV($csvArray)
    {
        $this->csvFile = new CSV(
            $this->getFileFolder(),
            $this->getFromVAI(),
            $this->getCSVHeader()
        );
    }

    protected function uploadCSV()
    {
        return $this->upload(
            $this->csvFile->getFilePath(),
            $this->getEtailRootFolder() .
            "/" .
            $this->getDestinationFolder() .
            "/" .
            $this->csvFile->getFileName()
        );
    }

    public function getCSVHeader()
    {
        return $this->csvHeader;
    }
}