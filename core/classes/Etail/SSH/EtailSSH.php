<?php

namespace Etail\SSH;

class EtailSSH
{

    protected $connection;
    protected $server;
    protected $username;
    protected $password;
    protected $login;
    protected $sftp;

    public function __construct()
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
        $this->connection = ssh2_connect($this->getServer());

        if(!$this->connection) die("Couldn't connect to {$this->server}");
    }

    protected function authenticateConnection()
    {
        $this->login = ssh2_auth_password(
            $this->getConnection(),
            $this->getUsername(),
            $this->getPassword()
        );
    }

    protected function setSFTP()
    {
        $this->sftp = ssh2_sftp($this->getConnection());
    }

    public function getConnection()
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

    // protected function upload($currentFileLocation, $fileDestination)
    // {
    //     return new EtailSSHUpload($currentFileLocation, $fileDestination);
    // }

    // protected function downloadFrom($currentFileLocation, $fileDestination)
    // {
    //     return new EtailSSHDownload($currentFileLocation, $fileDestination);
    // }
}
