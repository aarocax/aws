<?php 

namespace METRIC\App\Http;

use METRIC\App\Http\Request;

class Router
{
  private static $pattern;
  private static $request_uri;
  private static $request_method;
  private static $content_type;

  public static function get($pattern, $request_uri, $request_method, $content_type, $callback): void
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


  public static function post($pattern, $request_uri, $request_method, $content_type, $callback): void
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

  public static function on($regex, $requestUri, $callback): void
  {
    if(self::isMatch($regex, $requestUri)) {
      $callback(new Request(self::$request_method, self::$content_type), new Response());
    }
    return;
  }

  private static function isMatch(string $regex, string $requestUri): bool
  {
    $requestUri = (stripos($requestUri, "/") !== 0) ? "/" . $requestUri : $requestUri;
    $regex = str_replace('/', '\/', $regex);
    $regex = preg_replace('/(?<![\]\)])\?/', '\?', $regex);
    return preg_match('/^' . ($regex) . '$/', $requestUri, $matches, PREG_OFFSET_CAPTURE);
  }

}