<?php

namespace Etail\Inventory;

use CSV\CSV;

class EtailInventoryUpdate extends EtailInventory
{

    protected $csvFile;
    protected $csvHeader = [
        "LOC",
        "SKU",
        "QTY"
    ];
    protected $destinationFolder = "Inventory/In";

    public function __construct()
    {

        parent::__construct($this->interval);

        $this->createCSVWithUpdatedInventory();

        $this->uploadCSV();

    }

    protected function createCSVWithUpdatedInventory()
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getLocalDirectory(), $this->getUpdatedInventory(), $this->getCSVHeader());

    }

    protected function uploadCSV()
    {

        return $this->uploadInventoryToSSH($this->csvFile->getFilePath(), $this->getDestinationFolder() . "/" . $this->csvFile->getFileName());

    }

    public function getDestinationFolder()
    {

        return $this->destinationFolder;

    }

    public function getCSVHeader()
    {

        return $this->csvHeader;

    }

}