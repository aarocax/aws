<?php

/*******************************************************************************
 * Clase que devuelve la instancia de si misma. Para ser extendidad por Las clases
 * que contienen los parámetros de configuración
 *******************************************************************************/

namespace METRIC\App\Config;

class Singleton
{

  private static $instances = [];

  protected function __construct() { }

  protected function __clone() { }

  public function __wakeup()   {
      throw new \Exception("Cannot unserialize object");
  }

  public static function getInstance(): object
  {
    $subclass = static::class;
    if (!isset(self::$instances[$subclass])) {
      self::$instances[$subclass] = new static();
    }
    return self::$instances[$subclass];
  }

}