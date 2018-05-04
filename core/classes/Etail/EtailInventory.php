<?php

namespace Etail;

class EtailInventory
{

    //LOC
    //SKU
    //QTY

    public function __construct()
    {

        $this->getInventoryFromVAI();

        $this->getInventoryFromDB();

    }

    protected function getInventoryFromVAI()
    {

    }

    protected function getInventoryFromDB()
    {

    }

    protected function formatUpdatedInventoryForUpload()
    {

    }

    protected function uploadInventoryToFTP()
    {

    }

    protected function saveInventoryToDB()
    {

    }

}