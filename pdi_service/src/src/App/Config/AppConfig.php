<?php

namespace METRIC\App\Config;

use METRIC\App\Config\Singleton;
use METRIC\App\Service\Logger;

/*
 * Clase para contener la configuración de la aplicación y ser accesible desde
 * cualquier punto de la app.
 * 
 * La clase se inicializa en el archivo boostrap con los parámetros del archivo .env
 */

class AppConfig extends Singleton
{

  private $hashmap = [];
  private $logger;

  public function getValue(string $key): string
  {
    if (array_key_exists($key, $this->hashmap)) {
      return $this->hashmap[$key];
    } else {
      $this->logger = new Logger;
      $this->logger->error("No existe la varible de entorno: " . $key);
      return "";
    }
  }

  public function setValue(string $key, string $value): void
  {
    $this->hashmap[$key] = $value;
  }
}
