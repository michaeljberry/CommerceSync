<?php

namespace Etail;

use CSV\CSV;

class EtailInventoryFiveMinute extends EtailInventory
{

    protected $interval = null;
    protected $csvFile;
    protected $localDirectory;
    protected $destination = "Inventory" . DIRECTORY_SEPARATOR . "In";

    public function __construct()
    {

        parent::__construct($this->interval);

        $this->setDirectory();

        $this->createCSVWithUpdatedInventory();

        $this->uploadCSV();

    }

    protected function setDirectory()
    {

        $this->localDirectory = getenv('ETAIL_FTP_DIRECTORY');

    }

    protected function createCSVWithUpdatedInventory()
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getDirectory(), $this->getUpdatedInventory());

    }

    protected function uploadCSV()
    {

        $this->uploadInventoryToSSH($this->csvFile->getFilePath(), $this->getDestination() . "/" . $this->csvFile->getFileName());

    }

    public function getDestination()
    {

        return $this->destination;

    }

    public function getDirectory()
    {

        return $this->localDirectory;

    }

}