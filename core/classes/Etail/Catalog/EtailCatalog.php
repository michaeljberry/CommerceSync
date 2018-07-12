<?php

namespace Etail\Catalog;

use IBM;
use Etail\SSH\EtailSSHUploadCSV;

class EtailCatalog extends EtailSSHUploadCSV
{
    protected $vaiCatalog;

    public function __construct()
    {
        parent::__construct();

        $this->getCatalogFromVAI();
    }

    protected function getCatalogFromVAI()
    {
        $this->vaiCatalog = IBM::getEtailCatalog();
    }

    public function getFromVAI()
    {
        return $this->vaiCatalog;
    }
}