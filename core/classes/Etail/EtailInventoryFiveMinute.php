<?php

namespace Etail;

use CSV\CSV;

class EtailInventoryFiveMinute extends EtailInventory
{

    protected $interval = 15;
    protected $csvFile;
    protected $directory;

    public function __construct()
    {

        parent::__construct($this->interval);

        $this->setDirectory();

        $this->createCSVWithUpdatedInventory();

    }

    protected function setDirectory()
    {

        $this->directory = getenv('ETAIL_FTP_DIRECTORY');

    }

    protected function createCSVWithUpdatedInventory()
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getDirectory(), $this->getUpdatedInventory());

    }

    public function getDirectory()
    {

        return $this->directory;

    }

}