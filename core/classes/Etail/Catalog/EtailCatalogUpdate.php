<?php

namespace Etail\Catalog;

class EtailCatalogUpdate extends EtailCatalog
{

    protected $destinationFolder = "Catalog/In";
    protected $csvHeader = [];

    public function __construct()
    {

        parent::__construct();

        $this->createCSVWithCatalog();

        $this->uploadCSV();

    }

    protected function createCSVWithCatalog()
    {

        $this->createCSV($this->getVAICatalog());

    }

}