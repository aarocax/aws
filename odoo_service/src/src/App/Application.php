<?php

namespace METRIC\App;

use METRIC\App\Config\AppConfig;
use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;
use METRIC\App\Controller\OdooController;

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
			$this->logger->info("odoo live...");
			$res->status(200)->toText("odoo service live...");
			exit;
		});

		/*
		 * Enlace odoo pattern 1 -> https://serv.front1.bbva.ch/r/q1H/m/8099
		 */
		Router::get('/r/([a-zA-z0-9]{3,4})/m/([0-9]*)', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->logger->info("Odoo service /r/([a-zA-z0-9]{3,4})/m/([0-9]*):...");
			$response['odoo']['info']["http_code"] = "";
			$request_odoo_url = "";

			$url = $this->getRequestUrl($this->request_uri);

			$this->logger->info($url);

			$url_parts = parse_url($url);
			$this->logger->info($url_parts);
			$request_odoo_url = AppConfig::getInstance()->getValue("ODOO_ENDPOINT") . $url_parts['path'];

			// añade la query si la lleva la petición
			if (array_key_exists("query", $url_parts)) {
				$request_odoo_url .= "?" . $url_parts['query'];
			}

			$this->logger->info("request_odoo_url: " . $request_odoo_url);

			$response['odoo'] = (new OdooController())->redirectUrl([$request_odoo_url]);

			$this->logger->info($response['odoo']['info']);

			if ($response['odoo']['info']['http_code'] === 301) {
				$res->status(301)->toText($response['odoo']['info']['url']);
			} else {
				$res->status(301)->toText("");
			}

			exit;
		});

		/*
		 * Enlace odoo pattern 2 -> unsubscribe
		 * https://serv.front1.bbva.ch/mail/mailing/12345/unsubscribe?res_id=33149&email=miguel.rubio%40bbva.com&token=67bc1813c0969f21e3de87fcaddb610e7bf65001f97afafd91b9780173e27f5a0828309da561a9df7eedce530cc52cbf3811fdaaa1d6b0daa39ee29429c438b8
		 * 
		 */
		Router::get('/mail/mailing/([0-9]*)/unsubscribe?.*', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->logger->info("Odoo service /mail/mailing/([0-9]*)/unsubscribe?.* ...");

			$response['odoo']['info']["http_code"] = "";
			$request_odoo_url = "";

			$redirect_url = "";

			$url = $this->getRequestUrl($this->request_uri);

			$this->logger->info($url);

			$url_parts = parse_url($url);

			$this->logger->info($url_parts);

			$request_odoo_url = AppConfig::getInstance()->getValue("ODOO_ENDPOINT") . $url_parts['path'];

			// añade la query si la lleva la petición
			if (array_key_exists("query", $url_parts)) {
				$request_odoo_url .= "?" . $url_parts['query'];
			}

			$this->logger->info("request_odoo_url: " . $request_odoo_url);

			$response['odoo'] = (new OdooController())->redirectUrl([$request_odoo_url]);

			$this->logger->info($response['odoo']['info']);

			$redirect_url = 'https://www.bbva.ch';

			$res->status(200)->toText($redirect_url);
		});

		/*
		 * Enlace odoo pattern 3 -> tracking
		 * https://serv.front1.bbva.ch:8442/mail/track/9852/a22233f7a865b22faf82e4d87c6e2ccccaae65f9ffb83b81c7bfeb6f5c9cd7ab/blank.gif
		 * 
		 */
		Router::get('/mail/track/\d+/([a-zA-z0-9])*/.*', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->logger->info("Odoo service /mail/track/\d+/([a-zA-z0-9])*/.* ...");
			$response['odoo']['info']["http_code"] = "";
			$request_odoo_url = "";

			$url = $this->getRequestUrl($this->request_uri);

			$this->logger->info($url);

			$url_parts = parse_url($url);

			$this->logger->info($url_parts);

			$request_odoo_url = AppConfig::getInstance()->getValue("ODOO_ENDPOINT") . $url_parts['path'];

			// añade la query si la lleva la petición
			if (array_key_exists("query", $url_parts)) {
				$request_odoo_url .= "?" . $url_parts['query'];
			}

			$this->logger->info("request_odoo_url: " . $request_odoo_url);

			$response['odoo'] = (new OdooController())->redirectUrl([$request_odoo_url]);

			$this->logger->info($response['odoo']['info']);

			$res->status(301)->toText("");
		});
	}

	private function getRequestUrl($requestUri)
	{
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ?  $link = "https" : $link = "http";
		return $protocol . "://" . $_SERVER['HTTP_HOST'] . $requestUri;
	}

}
