<?php

namespace Etail\Catalog;

use IBM;
use Etail\Etail;

class EtailCatalog extends Etail
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