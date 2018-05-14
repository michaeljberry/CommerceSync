<?php

namespace Etail\Inventory;

class EtailInventoryUpdate extends EtailInventory
{

    protected $destinationFolder = "Inventory/In";
    protected $csvHeader = [
        "LOC",
        "SKU",
        "QTY"
    ];

    public function __construct()
    {

        parent::__construct();

        $this->createCSV($this->getFromVAI());

        $this->uploadCSV();

    }

}