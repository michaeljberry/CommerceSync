<?php

namespace Etail\SSH;

use Exception;

use Etail\Etail;

class EtailSSHUpload extends Etail
{
    protected $fileContents;
    protected $fileStream;
    protected $filePath;

    public function __construct()
    {
        parent::__construct();
    }

    public function upload($filePath, $destinationFilePath)
    {
        $this->filePath = $filePath;
        $this->fileStream = new EtailSSHStream($destinationFilePath);

        $this->uploadFile($this->fileStream);
    }

    protected function extractFileContents()
    {
        $this->fileContents = file_get_contents($this->filePath);

        if ($this->fileContents === false)

            throw new Exception("Could not open local file: " . $this->filePath);
    }

    protected function writeFileContentsToEtailServer($stream)
    {
        if (fwrite($stream->getStream(), $this->getFileContents()) === false)

            throw new Exception("Could not send data from file: " . $this->filePath);
    }

    protected function uploadFile(EtailSSHStream $stream)
    {
        $this->extractFileContents();

        $this->writeFileContentsToEtailServer($stream);

        $stream->close();
    }

    public function getFileContents()
    {
        return $this->fileContents;
    }

    public function getDestinationFolder()
    {
        return $this->destinationFolder;
    }

    // public function getStream()
    // {
    //     return $this->fileStream;
    // }
}