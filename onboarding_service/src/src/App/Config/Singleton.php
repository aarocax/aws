<?php

namespace METRIC\App\Config;

/**
 * Singleton
 * Crea una instancia de una clase si no existe o retorna la ya existente.
 */
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