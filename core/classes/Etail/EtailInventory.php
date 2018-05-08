<?php

namespace Etail;

<<<<<<< HEAD
use IBM;
use models\channels\DBInventory;

class EtailInventory
{

    protected $vaiInventory;
    protected $dbInventory;
    protected $updatedInventory;
    protected $datedFilename;

    public function __construct($interval = null)
=======
class EtailInventory
{

    //LOC
    //SKU
    //QTY

    public function __construct()
>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
    {

        $this->getInventoryFromVAI();

<<<<<<< HEAD
        $this->updateInventoryInDB();

        $this->getUpdatedInventoryFromDB($interval);

        $this->setDatedFilename();
=======
        $this->getInventoryFromDB();
>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973

    }

    protected function getInventoryFromVAI()
    {

<<<<<<< HEAD
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

=======
    }

    protected function getInventoryFromDB()
    {

>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
    }

    protected function formatUpdatedInventoryForUpload()
    {

    }

<<<<<<< HEAD
    protected function uploadInventoryToSSH()
=======
    protected function uploadInventoryToFTP()
>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
    {

    }

<<<<<<< HEAD
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

=======
    protected function saveInventoryToDB()
    {

>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
    }

}