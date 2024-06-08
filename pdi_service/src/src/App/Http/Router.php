<?php 

namespace METRIC\App\Http;

use METRIC\App\Http\Request;
use METRIC\App\Http\Response;

class Router
{

  private static $pattern;
  private static $request_uri;
  private static $request_method;
  private static $content_type;

  public static function get($pattern, $request_uri, $request_method, $content_type, $callback)
  {
    self::$pattern = $pattern;
    self::$request_uri = $request_uri;
    self::$request_method = $request_method;
    self::$content_type = $content_type;
    if (strcasecmp($request_method, 'GET') !== 0) {
      return;
    }

    self::on(self::$pattern, self::$request_uri, $callback);
  }


  public static function post($pattern, $request_uri, $request_method, $content_type, $callback)
  {

    self::$pattern = $pattern;
    self::$request_uri = $request_uri;
    self::$request_method = $request_method;
    self::$content_type = $content_type;
    if (strcasecmp($request_method, 'POST') !== 0) {
      return;
    }

    self::on(self::$pattern, self::$request_uri, $callback);

  }

  public static function on($regex, $request_uri, $callback)
  {
    $params = $request_uri;
    $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
    $regex = str_replace('/', '\/', $regex);
    $regex = preg_replace('/(?<![\]\)])\?/', '\?', $regex);
    $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);
    if ($is_match) {
      array_shift($matches); // elimina primer parámetro
      $params = array_map(function ($param) {
        return $param[0];
      }, $matches);
      $callback(new Request(self::$request_method, self::$content_type), new Response());
    }
  }
}