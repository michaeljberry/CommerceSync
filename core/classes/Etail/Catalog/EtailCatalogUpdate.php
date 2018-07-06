<?php

namespace Etail\Catalog;

class EtailCatalogUpdate extends EtailCatalog
{

    protected $destinationFolder = "Catalog/In";
    protected $csvHeader = [
        "DIVISION",
        "CLASS",
        "SKU",
        "S_TITLE",
        "TITLE",
        "MANUF",
        "MANUF_NO",
        "IDENT",
        "AVG_COST",
        "PRICE",
        "MSRP",
        "MAP",
        "UOM_DESC",
        "UOM",
        "UOM_DESC_S",
        "UOM_SELL"
    ];

    public function __construct()
    {

        parent::__construct();

        $this->createCSV($this->getFromVAI());

        $this->uploadCSV();

    }

}