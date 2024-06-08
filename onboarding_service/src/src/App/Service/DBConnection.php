<?php

namespace METRIC\App\Service;

use METRIC\App\Controller\BaseController;
use METRIC\App\Config\AppConfig;

class DBConnection extends BaseController {

  private $host, $database, $username, $password, $connection, $port;
  
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
      // conexión con certificado si está en infraestructura bbva
      if (AppConfig::getInstance()->getValue("CONTEXT") == "bbva") {
        $mysqli = mysqli_init();
        $mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
        $mysqli->ssl_set(NULL, NULL, NULL, "/etc/ssl/certs",  NULL);
        $mysqli->real_connect($this->host, $this->username, $this->password, $this->database, $this->port);
        $this->connection = $mysqli;
      } else {
        $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
      }

      if ($this->connection->connect_errno) {
        $this->logError($this->connection->connect_errno);
      }
    } catch (\Throwable $th) {
      $this->logError("error conexión a bbdd...");
    }
  }

  public function close() {
    $this->connection->close();
  }

  public function query($query) {
    $this->logInfo($query);
    $result = $this->connection->query($query);
    if ($this->connection->error) {
      $this->logError($this->connection->error);
    }
    return $result;
  }

}