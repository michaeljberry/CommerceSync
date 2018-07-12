<?php

namespace Etail\Inventory;

use IBM;
use Etail\EtailVAIConnection;
use models\channels\DBInventory;
use Etail\SSH\EtailSSHUploadCSV;

class EtailInventory extends EtailSSHUploadCSV implements EtailVAIConnection
{
    protected $vaiInventory;
    protected $dbInventory;
    protected $updatedInventory;

    public function __construct()
    {
        parent::__construct();

        $this->getInventoryFromVAI();

        $this->updateInventoryInDB();

        $this->getUpdatedInventoryFromDB();
    }

    protected function getInventoryFromVAI()
    {
        $this->vaiInventory = IBM::getEtailInventory();
    }

    protected function updateInventoryInDB()
    {
        DBInventory::updateEtailInventory($this->getVAIInventory());
    }

    public function getUpdatedInventoryFromDB()
    {
        $this->updatedInventory = DBInventory::getUpdatedInventory($this->getInterval());
    }

    public function getVAIInventory()
    {
        return $this->vaiInventory;
    }

    public function getDBInventory()
    {
        return $this->dbInventory;
    }

    public function getFromVAI()
    {
        return $this->updatedInventory;
    }
}