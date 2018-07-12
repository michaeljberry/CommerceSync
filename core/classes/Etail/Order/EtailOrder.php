<?php

namespace Etail\Order;

use Etail\SSH\EtailSSHDownload;

class EtailOrder extends EtailSSHDownload
{
    protected $orderFileName;

    // SSH into Etail's SalesOrders/Out FTP directory
    // If there are .xml files
    // Iterate through each
    // Copy .xml file to VAI FTP directory
    // If copy was successful
    // Move Etail file to complete FTP directory
    // If not try to copy again
    // End iteration
    // Close SSH

    public function __construct()
    {
        parent::__construct();
    }
}