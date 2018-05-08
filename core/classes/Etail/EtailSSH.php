<?php

namespace Etail;

class EtailSSH
{

    protected $connection;
    protected $server;
    protected $username;
    protected $password;
    protected $login;
    protected $parentFolder = "ChesbroMusic";
    protected $currentFileLocation;
    protected $fileDestination;
    protected $fileDestinationPath;
    protected $sftp;

    public function __construct($currentFileLocation, $fileDestination)
    {

        $this->setConnectionParameters();

        $this->connectToFTPServer();

        $this->authenticateInFTPServer();

        $this->setSFTP();

    }

    protected function setConnectionParameters()
    {

        $this->setServer();

        $this->setUsername();

        $this->setPassword();

    }

    private function setServer()
    {

        $this->server = getenv("ETAIL_FTP");

    }

    private function setUsername()
    {

        $this->username = getenv("ETAIL_USERNAME");

    }

    private function setPassword()
    {

        $this->password = getenv("ETAIL_PASSWORD");

    }

    protected function connectToFTPServer()
    {

        $this->connection = ssh2_connect($this->server);

        if(!$this->connection) die("Couldn't connect to {$this->server}");

    }

    protected function authenticateInFTPServer()
    {

        $this->login = ssh2_auth_password($this->getConnection(), $this->getUsername(), $this->getPassword());

    }

    protected function setSFTP()
    {

        $this->sftp = ssh2_sftp($this->getConnection());

    }

    protected function getConnection()
    {

        return $this->connection;

    }

    protected function getServer()
    {

        return $this->server;

    }

    protected function getUsername()
    {

        return $this->username;

    }

    protected function getPassword()
    {

        return $this->password;

    }

    public function getSFTP()
    {

        return $this->sftp;

    }

}
