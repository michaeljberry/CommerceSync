<?php

namespace Etail\Inventory;

use IBM;
use Etail;
use Etail\SSH\EtailSSHUpload;
use models\channels\DBInventory;

class EtailInventory extends Etail
{

    protected $vaiInventory;
    protected $dbInventory;
    protected $updatedInventory;

    public function __construct($interval = null)
    {

        parent::__construct();

        $this->getInventoryFromVAI();

        $this->updateInventoryInDB();

        $this->getUpdatedInventoryFromDB($interval);

    }

    protected function getInventoryFromVAI()
    {

        $this->vaiInventory = IBM::getEtailInventory();

    }

    public function updateInventoryInDB()
    {

        DBInventory::updateEtailInventory($this->getVAIInventory());

    }

    public function getUpdatedInventoryFromDB($interval)
    {

        $this->updatedInventory = DBInventory::getUpdatedInventory($interval);

    }

    protected function uploadInventoryToSSH($currentFileLocation, $fileDestination)
    {

        return new EtailSSHUpload($currentFileLocation, $fileDestination);

    }

    public function getVAIInventory()
    {

        return $this->vaiInventory;

    }

    public function getDBInventory()
    {

        return $this->dbInventory;

    }

    public function getUpdatedInventory()
    {

        return $this->updatedInventory;

    }

}