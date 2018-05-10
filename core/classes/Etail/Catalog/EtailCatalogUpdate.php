<?php

namespace Etail\Catalog;

use CSV\CSV;

class EtailCatalogUpdate extends EtailCatalog
{

    protected $csvFile;
    protected $csvHeader = [];
    protected $destinationFolder = "Category/In";

    public function __construc()
    {

        parent::__construct();

        $this->createCSVWithCatalog();

        $this->uploadCSV();

    }

    protected function createCSVWithCatalog()
    {

        $this->csvFile = new CSV($this->getDatedFileName(), $this->getLocalDirectory(), $this->getVAICatalog(), $this->getCSVHeader());

    }

    protected function uploadCSV()
    {



    }

}