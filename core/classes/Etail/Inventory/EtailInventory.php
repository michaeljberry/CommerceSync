<?php

namespace Etail\Inventory;

use IBM;
use Etail\SSH\EtailSSHUpload;
use models\channels\DBInventory;

class EtailInventory
{

    protected $vaiInventory;
    protected $dbInventory;
    protected $updatedInventory;
    protected $datedFilename;

    public function __construct($interval = null)
    {

        $this->getInventoryFromVAI();

        $this->updateInventoryInDB();

        $this->getUpdatedInventoryFromDB($interval);

        $this->setDatedFilename();

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

    protected function setDatedFilename()
    {

        $this->datedFilename = date('Y-m-d-H-i');

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

    public function getDatedFileName()
    {

        return $this->datedFilename;

    }

}