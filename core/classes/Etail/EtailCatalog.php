<?php

namespace Etail;

class EtailCatalog
{

    public function __construct()
    {

        $this->getCatalog();

        $this->formatCatalogForUpload();

        $this->uploadCatalog();

    }

}