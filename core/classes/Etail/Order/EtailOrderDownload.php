<?php

namespace Etail\Order;

use controllers\channels\FTPController;

class EtailOrderDownload extends EtailOrder
{
    protected $downloadFolder = "SalesOrders/Out";
    protected $destinationFolder = "";
    protected $foldersToIgnore = [
        ".",
        "..",
        "complete",
        "error",
        "Working",
        "retry"
    ];

    public function __construct()
    {
        parent::__construct();

        $this->downloadOrdersFromEtail();
    }

    protected function downloadOrdersFromEtail()
    {
        $this->download($this->getDownloadFolder());
    }

    public function getDownloadFolder()
    {
        return $this->downloadFolder;
    }
}