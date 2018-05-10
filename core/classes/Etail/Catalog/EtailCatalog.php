<?php

namespace Etail\Catalog;

use IBM;
use Etail;
use CSV\CSV;

class EtailCatalog extends Etail
{

    protected $csvFile;
    protected $vaiCatalog;
    protected $csvHeader = [
        "LOC",
        "SKU",
        "QTY"
    ];

    public function __construct()
    {

        parent::__construct();

        $this->getCatalogFromVAI();

        $this->createCSVWithCatalog();

    }

    protected function getCatalogFromVAI()
    {

        $this->vaiCatalog = IBM::getEtailCatalog();

    }

    public function getVAICatalog()
    {

        return $this->vaiCatalog;

    }

    public function getCSVHeader()
    {

        return $this->csvHeader;



}