<?php

namespace Etail\SSH;

use Exception;

class EtailSSHStream extends EtailSSH
{
    protected $stream;

    public function __construct($path, $directory = false, $mode = 'x')
    {
        parent::__construct();

        $this->setStream($path, $directory, $mode);
    }

    protected function setStream($path, $directory, $mode)
    {
        $sftpConnection = "ssh2.sftp://" . intval($this->getSFTP()) . "/" . $path;

        if ($directory) {
            $sftpConnection .= "/";
            $this->stream = opendir($sftpConnection);

            if (!$this->getStream())
                throw new Exception("Could not open file: " . $path);

            return $this->getStream();
        }

        $this->stream = fopen($sftpConnection, $mode);

        if (!$this->getStream())
            throw new Exception("Could not open file: " . $path);

        return $this->stream;
    }

    public function close($directory = false)
    {
        if ($directory) {
            closedir($this->getStream());
            return;
        }

        fclose($this->stream);
    }

    public function getStream()
    {
        return $this->stream;
    }
}