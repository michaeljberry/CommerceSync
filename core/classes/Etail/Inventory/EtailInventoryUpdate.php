<?php

namespace Etail\Inventory;

use CSV\CSV;

class EtailInventoryUpdate extends EtailInventory
{

    protected $csvFile;
    protected $csvHeader = ["LOC", "SKU", "QTY"];
    protected $localDirectory;
    protected $destination = "Inventory/In";

    public function __construct()
    {

        parent::__construct($this->interval);

        $this->setDirectory();

        $this->createCSVWithUpdatedInventory();

        // $this->uploadCSV();

    }

    protected function setDirectory()
    {

        $this->localDirectory = getenv('ETAIL_FTP_DIRECTORY');

    }

    protected function createCSVWithUpdatedInventory()
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getDirectory(), $this->getUpdatedInventory(), $this->getCSVHeader());

        $this->csvFile->prependToCSVArray($this->getCSVHeader());

        print_r($this->csvFile->getCSVArray());

    }

    protected function uploadCSV()
    {

        return $this->uploadInventoryToSSH($this->csvFile->getFilePath(), $this->getDestination() . "/" . $this->csvFile->getFileName());

    }

    public function getDestination()
    {

        return $this->destination;

    }

    public function getDirectory()
    {

        return $this->localDirectory;

    }

    public function getCSVHeader()
    {

        return $this->csvHeader;

    }

}