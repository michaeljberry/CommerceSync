<?php

namespace Etail\Tracking;

use IBM;
use Etail\EtailVAIConnection;
use models\channels\DBTracking;
use Etail\SSH\EtailSSHUploadCSV;

class EtailTracking extends EtailSSHUploadCSV implements EtailVAIConnection
{
    protected $vaiTracking;
    protected $dbTracking;

    public function __construct()
    {
        parent::__construct();

        $this->getTrackingFromVAI();

        $this->updateTrackingInDB();

        $this->getTrackingFromDB();

        // $this->getUnshippedOrders();

        // $this->getTrackingForOrder($orderNumber);

        // $this->formatTrackingForUpload();

        // $this->uploadTracking();
    }

    protected function getTrackingFromVAI()
    {
        $this->vaiTracking = IBM::getEtailTracking();
    }

    protected function updateTrackingInDB()
    {
        DBTracking::updateEtailTracking($this->getVAITracking());
    }

    public function getTrackingFromDB()
    {
        $this->dbTracking = DBTracking::getTracking($this->getInterval());
    }

    public function getVAITracking()
    {
        return $this->vaiTracking;
    }

    public function getDBTracking()
    {
        return $this->dbTracking;
    }

    public function getFromVAI()
    {
        return $this->vaiTracking;
    }
}