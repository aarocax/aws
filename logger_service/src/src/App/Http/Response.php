<?php

namespace METRIC\App\Http;

class Response
{
  private $status = 200;

  public function status(int $code)
  {
    $this->status = $code;
    return $this;
  }

  /*
   * Devolver la respuesta como un string de texto. Cuando la respuesta del
   * proxy server ya está en formato JSON.
   */
  public function toText($data = null): void
  {
    http_response_code($this->status);
    header('Content-Type: text/plain; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: content-type");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    echo $data;
  }


  public function toJSON($data = []): void
  {
    http_response_code($this->status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: content-type");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    echo json_encode($data, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_LINE_TERMINATORS);
  }

  public function toHTML($data): void
  {
    http_response_code($this->status);
    header('Content-Type: text/html; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: content-type');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    echo $data;
  }
}
