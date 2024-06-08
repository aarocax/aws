<?php

namespace METRIC\App;

use METRIC\App\Config\AppConfig;
use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;
use METRIC\App\Controller\ContactController;

class Application
{
    private $request_uri;
	private $request_method;
	private $content_type;
	private $logger;
    public function __construct(string $request_uri, string $request_method, string $content_type)
    {
        $this->request_uri = $request_uri;
		$this->request_method = $request_method;
		$this->content_type = $content_type;
        $this->logger = new Logger;

        Router::get('/live', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$res->status(200)->toText("contact live...");
			exit;
		});

        Router::post('/destination=contact', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$data = $req->getBody();

			$this->logger->info("/destination=contact...");
			$this->logger->info($data);

			$response = [];

			$response = (new ContactController())->send($data);

			$res->status(200)->toJSON([
				"error" => $response["error"]
			]);

			exit;
		});
    }
}