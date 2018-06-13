<?php

namespace Etail\Tracking;

class EtailTrackingUpdate extends EtailTracking
{

    protected $destinationFolder = "Tracking/In";
    protected $csvHeader = [
        "ORDER_NUMBER",
        "TRACKING_NUMBER",
        "CARRIER",
        "MAIL_CLASS",
        "DATE_SHIPPED",
        "BILLED_WEIGHT",
        "ACTUAL_WEIGHT",
        "POSTAGE_COST"
    ];

    public function __construct()
    {

        parent::__construct();

        $this->createCSV($this->getFromVAI());

        $this->uploadCSV();

    }

}