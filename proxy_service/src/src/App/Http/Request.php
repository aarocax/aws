<?php

namespace METRIC\App\Http;

use METRIC\App\Logger\Logger;

class Request
{
    public $params;
    public $reqMethod;
    public $reqPort;
    public $contentType;
    private $logger;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->reqPort = trim($_SERVER['SERVER_PORT']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $this->logger = new Logger;
        $this->logger->info("[METHOD]: " . $this->reqMethod);
        $this->logger->info("[PORT]: " . $this->reqPort);
    }

    /**
     * 
     * Obtener variables del body enviados por post 
     * body: "web_time=3-5-2022+10%3A46&web_device=desktop&web_browser=Google+Chrome+or+Chromium&web_language=es&web_session_time=0%3A0%3A3"
     * Headers: Content-Type: application/x-www-form-urlencoded
     * Return: array con las variables y su contenido []
     **/
    public function getBody()
    {
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $body = [];

        // RAW application/x-www-form-urlencoded
        $body = filter_input_array(INPUT_POST);

        $this->logger->info("[REQUEST DATA]:");
        $this->logger->info($body);

        return $body;
    }

    /**
     * Obtener datos del body enviados por post en formato json
     * body: { "email": "eve.holt@reqres.in", "password": "pistol"}
     * Headers: Content-Type: application/json
     * Return: array con el contenido json
     **/
    public function getJSON()
    {
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        if (!str_contains(strtolower($this->contentType), 'application/json')) {
            return [];
        }

        // RAW post data.
        $content = trim(file_get_contents("php://input"));

        $this->logger->info("[REQUEST DATA]:");
        $this->logger->info($content);

        $decoded = json_decode($content);

        return $decoded;
    }

    public function getUrl()
    {
        $this->logger->info($this->reqMethod);
        $this->logger->info($this->reqPort);
        $this->logger->info($_SERVER['HOST']);

    }
}