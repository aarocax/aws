<?php 

namespace METRIC\App\Http;

use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;

class Router
{

  private static $route;
  private static $logger;

  public static function get($route, $callback)
  {
    self::$route = $route;
    if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
      return;
    }

    self::on($route, $callback);
  }

  public static function post($route, $callback)
  {
    self::$route = $route;
    if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
        return;
    }

    self::on($route, $callback);
  }

  public static function on($regex, $cb)
  {
    $params = $_SERVER['REQUEST_URI'];
    $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
    $regex = str_replace('/', '\/', $regex);
    $regex = str_replace('?', '\?', $regex); // interrogante de los parámetros
    $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);
    
    if ($is_match) {
      self::$logger = new Logger;
      self::$logger->info("[MATCH ROUTE]: " . self::$route);
      $params = array_map(function ($param) {
        return $param[0];
      }, $matches);
      $cb(new Request($params), new Response());
    }
      
  }

}