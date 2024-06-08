<?php

namespace METRIC\App;

use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;
use METRIC\App\Config\AppConfig;

use METRIC\App\Service\SendData;

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

		$url = $this->getRequestUrl();

		if (!$this->isValidUrlFormat($url)) {
			exit(0);
		}

		Router::get('/live', function (Request $req, Response $res) {
			$res->status(200)->toText("proxy service live...");
			exit;
		});

		/*
		 * token de xpressid
		 */
		Router::post('/destination=xpressid', function (Request $req, Response $res) {
			$data = $req->getBody();

			$response = SendData::send("/destination=xpressid", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(200)->toText($response);

			exit;
		});

		/*
		 * Proceso de onboarding desde la aprimera página del formulario
		 */
		Router::post('/(destination=onboarding$|destination=onboarding&test=1$)', function (Request $req, Response $res) {
			$data = $req->getBody();

			$this->logger->info($data);

			$response = SendData::send("/destination=onboarding", ["content-type: application/x-www-form-urlencoded"], $data);

			// $resp = json_decode($response, true);
			// $resp["data"]["esign"]["library_document_id"] = "{...}";
			// $resp["data"]["xpressid"]["validation_id"] = "{...}";
			// $resp["data"]["xpressid"]["info"] = "{...}";
			// $resp["data"]["esign_webhook"]["info"] = "{...}";
			// $response = json_encode($resp);

			$res->status(200)->toJSON($response);
			exit;
		});

		/*
		 * Proceso de onboarding retargeting
		 */
		Router::post('/destination=updateCase', function (Request $req, Response $res) {
			$data = $req->getBody();

			$response = SendData::send("/destination=updateCase", ["content-type: application/x-www-form-urlencoded"], $data);

			$resp = json_decode($response, true);

			$resp["data"]["atfinity"] = "";
			$response = json_encode($resp);

			$res->status(200)->toText($response);

			exit;
		});

		/*
		 * Obtiene un nuevo tokend e XpressID y lo presenta en pantalla cuando se scanea el QR presentado en la pantalla del ordenador y se redirige al móvil.
		 */
		Router::post('/destination=mobileRedirect', function (Request $req, Response $res) {
			$data = $req->getBody();

			$response = SendData::send("/destination=mobileRedirect", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(200)->toText($response);
			exit;
		});

		/*
		 * Endpoint al que llama eSign cuando la firma del documento se realiza o se declina. El endpoint captura el evento y modifica el estado de Atfinity
		 * y de la bbdd a 3 ó -3
		 */
		Router::post('/destination=esignWebhook', function (Request $req, Response $res) {

			$data = [file_get_contents('php://input')];

			$response = SendData::send("/destination=esignWebhook", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(202)->toText($response);
			exit;
		});

		/*
		 * Endpoint al que se llama cuando XpressID produce un evento tipo "ProcessError". Confirma el caso en validas dejándolo disponible para boidas.
		 * Tambien verifica los scores global y de documentos.
		 */
		Router::post('/destination=validas', function (Request $req, Response $res) {

			$data = $req->getBody();

			$this->logger->info($data);

			$response = SendData::send(
				"/destination=validas",
				["content-type: application/x-www-form-urlencoded"],
				$data
			);

			$resp = json_decode($response, true);
			$resp["data"]["validas"] = "{...}";
			$resp["data"]["atfinity"] = "{...}";
			$response = json_encode($resp);

			$res->status(200)->toText(
				$response
			);

			exit;
		});

		/*
		 * Obtiene el estado de un caso. Este endpoint es llamado desde el frontend para sincronizar la pantalla tanto del ord. como del movil con el estado del caso.
		 */
		Router::post('/destination=state', function (Request $req, Response $res) {

			$data = $req->getBody();

			$response = SendData::send("/destination=state", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(200)->toText($response);

			exit;
		});

		Router::post('/destination=email', function (Request $req, Response $res) {
			$data = $req->getBody();

			$this->logger->info("proxy llamamos a onboarding...");

			$response = SendData::send("/destination=email", ["content-type: application/x-www-form-urlencoded"], $data);

			$this->logger->info($response);
			$this->logger->info("proxy fin de la llamada...");

			$res->status(200)->toText("proxy destination=email end...");

			exit;
		});

		/*
		 * Verifica si el hash existe en la bbdd
		 */
		Router::post('/destination=checkHash', function (Request $req, Response $res) {

			$data = $req->getBody();

			$response = SendData::send("/destination=checkHash", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(200)->toText($response);

			exit;
		});

		/*
		 * Obtener hash
		 * url param (hash 64 caracteres)
		 */
		Router::get('/hash/([a-zA-z0-9]*)', function (Request $req, Response $res) {

			$data = $req->getBody();

			$response = SendData::send("/hash/" . $req->params[0], ["content-type: application/x-www-form-urlencoded"], [$data]);

			$res->toJSON([
				"response" => $response,
				"params" => $req->params,
			]);

			exit;
		});

		/*
		 * Datos del formulario de contacto hazte-cliente. Se envía un email notificando el contacto
		 */
		Router::post('/destination=contact', function (Request $req, Response $res) {
			$data = $req->getBody();

			$response = SendData::sendContact("/destination=contact", ["content-type: application/x-www-form-urlencoded"], $data);

			$res->status(200)->toText($response);
			exit;
		});

		/*
		 * Enlace odoo pattern 1 -> https://serv.front1.bbva.ch/r/q1H/m/8099
		 */
		Router::get('/r/([a-zA-z0-9]{3,4})/m/([0-9]*)', function (Request $req, Response $res) {

			$this->logger->info("[params:]");
			$this->logger->info($req->params);

			$response = SendData::redirectionOdoo($req->params[0], [], [], 'GET');

			$this->logger->info($response);

			if ($response !== "") {
				header("Location: " . $response);
			}

			exit;
		});

		/*
		 * Enlace odoo pattern 2 -> unsubscribe
		 * https://serv.front1.bbva.ch/mail/mailing/12345/unsubscribe?res_id=33149&email=miguel.rubio%40bbva.com&token=67bc1813c0969f21e3de87fcaddb610e7bf65001f97afafd91b9780173e27f5a0828309da561a9df7eedce530cc52cbf3811fdaaa1d6b0daa39ee29429c438b8
		 * 
		 */
		Router::get('/mail/mailing/([0-9]*)/unsubscribe?.*', function (Request $req, Response $res) {
			$this->logger->info("[params:]");
			$this->logger->info($req->params);

			$response = SendData::redirectionOdoo($req->params[0], [], [], 'GET');

			$this->logger->info($response);

			if ($response !== "") {
				header("Location: " . $response);
			}

			exit;
		});

		/*
		 * Enlace odoo pattern 3 -> tracking
		 * https://serv.front1.bbva.ch:8442/mail/track/9852/a22233f7a865b22faf82e4d87c6e2ccccaae65f9ffb83b81c7bfeb6f5c9cd7ab/blank.gif
		 * 
		 */
		Router::get('/mail/track/\d+/([a-zA-z0-9])*/.*', function (Request $req, Response $res) {
			$this->logger->info("[params:]");
			$this->logger->info($req->params);

			$response = SendData::redirectionOdoo($req->params[0], [], [], 'GET');

			//$res->status(200)->toText("");
			exit;
		});
	}

	/*
	* Sanitiza y valida que es una url correcta
	* parameters: $url String
	* return: true/false
	*/
	private function isValidUrlFormat($url)
	{
		$response = false;
		$url = filter_var($url, FILTER_SANITIZE_URL);
		if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== false) {
			$url = parse_url($url);
			if ($url["scheme"] === "https" || $url["scheme"] === "http") {
				$response = true;
			}
		}
		return $response;
	}

	private function getRequestUrl()
	{
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ?  "https" : "http";

		return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}
