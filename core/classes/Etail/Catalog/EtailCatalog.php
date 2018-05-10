<?php

namespace Etail\Catalog;

use IBM;

class EtailCatalog
{

    public function __construct()
    {

        $this->getCatalogFromVAI();

        $this->formatCatalogForUpload();

        $this->uploadCatalog();

    }

    protected function getCatalogFromVAI()
    {

        $this->vaiCatalog = IBM::getEtailCatalog();

    }

}