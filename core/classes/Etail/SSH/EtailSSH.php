<?php

namespace Etail\SSH;

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

        $this->setConnection();

        $this->authenticateConnection();

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

    protected function setConnection()
    {

        $this->connection = ssh2_connect($this->server);

        if(!$this->connection) die("Couldn't connect to {$this->server}");

    }

    protected function authenticateConnection()
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

    public function getUsername()
    {

        return $this->username;

    }

    public function getPassword()
    {

        return $this->password;

    }

    public function getSFTP()
    {

        return $this->sftp;

    }

}