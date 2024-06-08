<?php

namespace METRIC\App\Service;

use METRIC\App\Controller\BaseController;

class DBConnection extends BaseController {

  private $host, $database, $username, $password, $port, $connection;
  
  function __construct($host, $username, $password, $database, $port = 3306, $autoconnect = true) {

    parent::__construct();

    $this->host = $host;
    $this->database = $database;
    $this->username = $username;
    $this->password = $password;
    $this->port = $port;

    if($autoconnect) $this->open();
  }

  private function open() {
    try {
      $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
      if ($this->connection->connect_errno) {
        $this->logError($this->connection->connect_errno);
      }
    } catch (\Throwable $th) {
      $this->logError("Error al conectar con la bbdd");
      $this->logError($th->getTrace());
      // Enviar email de alerta al administrador
    }
  }

  public function close() {
    $this->connection->close();
  }

  public function query($query) {
    $this->logInfo($query);
    if ($this->connection) {
      $result = $this->connection->query($query);
      if ($this->connection->error) {
        $this->logError($this->connection->error);
      }
    } else {
      $result = false;
    }
    
    return $result;
  }

}