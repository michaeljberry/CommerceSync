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
    protected $folder;
    protected $action;

    public function __construct($action, $folder)
    {

        $this->setAction($action);

        $this->setFolder($folder);

        $this->setConnectionParameters();

        $this->connectToFTPServer();

	$this->authenticateInFTPServer();

    }

    protected function setConnectionParameters()
    {

        $this->setServer();

        $this->setUsername();

        $this->setPassword();

    }

    protected function setAction($action)
    {

        $this->action = $action;

    }

    protected function setFolder($folder)
    {

        $this->folder = $folder;

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

        print_r($this);

        $this->connection = ssh2_connect($this->server);
        if(!$this->connection) die("Couldn't connect to {$this->server}");

        print_r($this->connection);
    }

    protected function authenticateInFTPServer()
    {

        $this->login = ssh2_auth_password($this->getConnection(), $this->getUsername(), $this->getPassword());

    }

    public function uploadDocument()
    {

    }

    public function downloadDocument()
    {

    }

    protected function getConnection()
    {

        return $this->connection;

    }

    protected function getAction()
    {

        return $this->action;

    }

    protected function getFolder()
    {

        return $this->folder;

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

}
