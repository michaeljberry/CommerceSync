<?php

namespace Etail;

use Exception;

class EtailSSHUpload extends EtailSSH
{
    protected $stream;
    protected $csvFileContents;

    public function __construct($currentFileLocation, $fileDestination)
    {

        parent::__construct($currentFileLocation, $fileDestination);

        $this->setCurrentFileLocation($currentFileLocation);

        $this->setFileDestination($fileDestination);

        $this->setFileDestinationPath();

        // $this->uploadFile();

    }

    protected function setCurrentFileLocation($currentFileLocation)
    {

        $this->currentFileLocation = $currentFileLocation;

    }

    protected function setFileDestination($fileDestination)
    {

        $this->fileDestination = $fileDestination;

    }

    protected function setFileDestinationPath()
    {

        $this->fileDestinationPath = "/" . $this->getDestinationParentFolder() . "/" . $this->getFileDestination();

    }

    protected function setStream()
    {

        $this->stream = fopen("ssh2.sftp://" . $this->getSFTP() . $this->getFileDestinationPath(), 'x');

        if (!$this->stream)

            throw new Exception("Could not open file: " . $this->getFileDestinationPath());

    }

    protected function setCSVFileContents()
    {

        $this->csvFileContents = file_get_contents($this->getCurrentFileLocation());

        if ($this->csvFileContents === false)

            throw new Exception("Could not open local file: " . $this->getCurrentFileLocation());

    }

    protected function writeContentsToServer()
    {

        if (fwrite($this->getStream(), $this->getCSVFileContents()) === false)

            throw new Exception("Could not send data from file: " . $this->getCurrentFileLocation());

    }

    protected function closeStream()
    {

        fclose($this->stream);

    }

    protected function uploadCSV()
    {

        // ssh2_scp_send($this->getConnection(), $this->getCurrentFileLocation(), "ssh2.sftp://" . $this->getSFTP() . $this->getFileDestinationPath(), 0777);
        file_put_contents(
            "ssh2.sftp://" . $this->getUsername() . ":" . $this->getPassword() . "@" . $this->getServer() . ":22" . $this->getFileDestinationPath(),
            file_get_contents($this->getCurrentFileLocation())
        );

    }

    protected function uploadFile()
    {

        $this->setStream();

        $this->setCSVFileContents();

        $this->writeContentsToServer();

        $this->closeStream();

        // $this->uploadCSV();

    }

    public function getCurrentFileLocation()
    {

        return $this->currentFileLocation;

    }

    public function getDestinationParentFolder()
    {

        return $this->parentFolder;

    }

    public function getFileDestination()
    {

        return $this->fileDestination;

    }

    public function getFileDestinationPath()
    {

        return $this->fileDestinationPath;

    }

    public function getStream()
    {

        return $this->stream;

    }

    public function getCSVFileContents()
    {

        return $this->csvFileContents;

    }

}