<?php 

namespace METRIC\App\Config;

use METRIC\App\Config\Singleton;

/*
 * Clase para contener la configuración de la aplicación y ser accesible desde
 * cualquier punto de la app.
 * 
 * La clase se inicializa en el archivo boostrap con los parámetros del archivo .env
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