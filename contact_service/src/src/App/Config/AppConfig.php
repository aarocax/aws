<?php 

namespace METRIC\App\Config;

use METRIC\App\Config\Singleton;

/**
 * AppConfig
 * Permite guardar y obtener variables de configuración desde cualquier punto
 * de la aplicación 
 */
class AppConfig extends Singleton
{

  private $hashmap = [];

  public function getValue(string $key): string
  {
      return $this->hashmap[$key];
  }

  public function setValue(string $key, string $value): void
  {
      $this->hashmap[$key] = $value;
  }
}