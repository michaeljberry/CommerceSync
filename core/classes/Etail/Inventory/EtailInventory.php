<?php

namespace Etail\Inventory;

use IBM;
use Etail\Etail;
use Etail\EtailVAIConnection;
use models\channels\DBInventory;

class EtailInventory extends Etail implements EtailVAIConnection
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