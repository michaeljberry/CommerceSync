<?php

namespace Etail;

class Etail
{

    protected $datedFileName;
    protected $localDirectory;

    public function __construct()
    {

        $this->setDatedFileName();

        $this->setLocalDirectory();

    }

    protected function setDatedFileName()
    {

        $this->datedFileName = date('Y-m-d-H-i');

    }

    protected function setLocalDirectory()
    {

        $this->localDirectory = getenv('ETAIL_FTP_DIRECTORY');

    }

    public function getDatedFileName()
    {

        return $this->datedFileName;

    }

    public function getLocalDirectory()
    {

        return $this->localDirectory;

    }

}