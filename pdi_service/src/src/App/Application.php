<?php

namespace METRIC\App;

use METRIC\App\Controller\BaseController;
use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Controller\AtfinityController;
use METRIC\App\Controller\XpressIDController;
use METRIC\App\Controller\DBController;
use METRIC\App\Controller\EsignController;
use METRIC\App\HttpClient\AtfinityHttpClient;
use METRIC\App\HttpClient\XpressIdHttpClient;
use METRIC\App\HttpClient\EsignHttpClient;
use METRIC\App\Config\AppConfig;
use METRIC\App\Service\CheckRecaptcha;
use METRIC\App\Service\Utils;
use METRIC\App\Service\SecureToken;
use METRIC\App\Service\Emailer;

class Application extends BaseController
{

	public $request_uri;
	public $request_method;
	public $content_type;
	public $request_port;
	public $params;
	public $match;
	private $emailer;

	private $response = [];

	public function __construct(string $request_uri, string $request_method, string $content_type, string $request_port)
	{

		parent::__construct();

		$this->request_uri = $request_uri;
		$this->request_method = $request_method;
		$this->content_type = $content_type;
		$this->request_port = $request_port;
		$this->emailer = new Emailer();


		$this->logInfo($request_uri);

		$this->router();
	}

	private function router()
	{
		/**
		 * Endpoint para para verificar que el servicio está activo.
		 */
		Router::get('/destination=live', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$res->toText("live\n");
			exit;
		});

		/**
		 * Recupera la información de un caso para autorrellenar el formulario
		 */
		Router::post('/destination=case', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();
			$vars = json_decode($data["vars"], true);
			
			if ($vars["hash"] !== null) {
				$ddbbCase = (new DBController())->getCaseByHash($vars["hash"]);
				$this->logInfo("DDBB case: " . $ddbbCase["info"]["atfinity_case_id"]);
				$atfinityCase = (new AtfinityController(new AtfinityHttpClient()))->getCase($ddbbCase["info"]["atfinity_case_id"]);
				$this->logInfo("Atfinity case: " . $atfinityCase->data->id);
				foreach ($atfinityCase->data->fields as $key => $field) {
					$this->response["fields"][$key]["key"] = $field->key;
					$this->response["fields"][$key]["value"] = $field->value;
				}
			} else {
				$this->logInfo("Hash is null or invalid: " . $vars["hash"]);
				$this->response = false;
			}
			$res->status(200)->toJSON($this->response);
			exit;
		});

		/**
		 * Recibe la información del formulario rellenado por el cliente antes del submit sin incluir la los campos de país de residencia
		 * ni tipo de documento para la video identificación.
		 * Retorna la respuesta de Atfinity y el campo "Investor Profile Result" con el riesgo.
		 */
		Router::post('/destination=save', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$this->logInfo('[/destination=save]');

			$data = $req->getBody();

			if ($data === null) {
				$this->logError("[No hay datos en la llamada a destination=save]");
				$this->logError($data);
				$this->response["error"] = true;
				$res->status(200)->toJSON([
					"data" => $this->response
				]);
				exit;
			}

			if (!is_array($data)) {
				$this->logError("[Llamada a destination=save no es un array de datos]");
				$this->logError($data);
				$this->response["error"] = true;
				$res->status(200)->toJSON([
					"data" => $this->response
				]);
				exit;
			}

			if (!array_key_exists("vars", $data)) {
				$this->logError("[Llamada a destination=save no existe el índice vars en el array de datos]");
				$this->logError($data);
				$this->response["error"] = true;
				$res->status(200)->toJSON([
					"data" => $this->response
				]);
				exit;
			}

			$this->logInfo($data);

			parse_str($req->getBody()['vars'], $data);

			$this->logInfo("[datos de entrada:]");
			$this->logInfo($data);

			$this->response["error"] = false;
			$this->response['service'] = "save";
			$this->response['secure'] = [];
			$this->response['recaptcha'] = [];
			$this->response['atfinity'] = [];
			$this->response['atfinity']['investor_profile_result'] = [];

			$this->logInfo("RECAPTCHA_CHECK: " . AppConfig::getInstance()->getValue("RECAPTCHA_CHECK"));

			if (AppConfig::getInstance()->getValue("RECAPTCHA_CHECK") != "false") {
				$this->response['recaptcha'] = CheckRecaptcha::check($data["token"], AppConfig::getInstance()->getValue("RECAPTCHA_V3_SECRET_KEY"));
				$this->response["error"] = $this->response['recaptcha']["error"];
				if ($this->response["error"] === true) {
					$this->emailer->sendMail(
						AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
						"[PDI] recaptcha failed with an error",
						"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response['recaptcha']['info']  . "</p>",
						explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
					);
					echo json_encode($this->response);
					die();
				}
			}

			$document_type = Utils::getDocumentType();
			$atfinity_instance_id = null;
			$caseId = null;

			// get case from ddbb
			if ($this->response["error"] === false) {
				$hash = $data['hash'];
				$case = (new DBController())->getCaseByHash($hash);
				$this->logInfo($case);
				$atfinity_instance_id = $case['info']['atfinity_instance_id'];
				$caseId = $case['info']['atfinity_case_id'];
			}

			// send data to Atfinity
			if ($this->response["error"] === false) {
				//$hash = $data['hash'];
				$atfinityController = new AtfinityController(new AtfinityHttpClient());
				//$atfinityController->sendData($data);
				$this->response['atfinity'] = $atfinityController->updateCase($caseId, $atfinity_instance_id, 'pdi3_q11', $data['horizonte_inversion']);
				$this->response['error'] = $this->response['atfinity']['error'];
				// $this->response['atfinity'] = $atfinityController->updateCase($caseId, $atfinity_instance_id, 'pdi3_q02', $data['objetivos_inversion']);
				// $this->response['atfinity'] = $atfinityController->updateCase($caseId, $atfinity_instance_id, 'pdi3_q19', $data['productos_inversion']);
			}

			// get Investor Profile Result
			if ($this->response["error"] === false) {
				$atfinityController = new AtfinityController(new AtfinityHttpClient());
				$response = $atfinityController->getRisk($caseId);
				$this->response["error"] = $response["error"];
				$this->response['risk'] = $response;
			}

			// save data to ddbb
			if ($this->response["error"] === false) {

			}

			$processError = ($this->response["error"]) ? "true" : "false";

			$this->logInfo("[save process error: " . $processError . "]");
			$this->logInfo($this->response);

			$res->status(200)->toJSON([
				"data" => $this->response
			]);
			exit;
		});

		/**
		 * Recibe la información completa del formulario rellenado por el cliente incluyendo el país de residencia y tipo de docmento a utilizar
		 * para la video identificación. Retorna el estado del proceso incluyendo el token de XPressId para preesentar el QR e iniciar la vídeo 
		 * identificación.
		 */
		Router::post('/destination=pdi', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$this->logInfo('[/destination=pdi]');

			parse_str($req->getBody()['vars'], $data);

			$this->logInfo("[datos de entrada:]");
			$this->logInfo($data);

			$this->response["error"] = false;
			$this->response['service'] = "pdi";
			$this->response['secure'] = [];
			$this->response['recaptcha'] = [];
			$this->response['atfinity'] = [];
			$this->response['atfinity']['files'] = [];

			$this->logInfo("RECAPTCHA_CHECK: " . AppConfig::getInstance()->getValue("RECAPTCHA_CHECK"));

			if (AppConfig::getInstance()->getValue("RECAPTCHA_CHECK") != "false") {
				$this->response['recaptcha'] = CheckRecaptcha::check($data["token"], AppConfig::getInstance()->getValue("RECAPTCHA_V3_SECRET_KEY"));
				$this->response["error"] = $this->response['recaptcha']["error"];
				if ($this->response["error"] === true) {
					$this->emailer->sendMail(
						AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
						"[PDI] recaptcha failed with an error",
						"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response['recaptcha']['info']  . "</p>",
						explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
					);
					echo json_encode($this->response);
					die();
				}
			}

			//$document_type = Utils::getDocumentType();
			$document_type = 'ES2_ID';
			$atfinity_instance_id = null;
			$caseId = null;

			// get case from ddbb
			if ($this->response["error"] === false) {
				$hash = $data['hash'];
				$case = (new DBController())->getCaseByHash($hash);
				$this->logInfo($case);
				$atfinity_instance_id = $case['info']['atfinity_instance_id'];
				$caseId = $case['info']['atfinity_case_id'];
			}

			// get document files form Atfinity
			if ($this->response["error"] == false) {
				$this->response['atfinity']['files'] = (new AtfinityController(new AtfinityHttpClient()))->getFiles($caseId, 'es');
				$this->response["error"] = $this->response["atfinity"]["files"]["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			// send Atfinity files to Esing
			if ($this->response["error"] == false) {
				$eSignData = [
					"file" => $this->response["atfinity"]["files"]["file"],
					"language" => $data["web_language"],
					"name" => $caseId
				];
				$this->response['esign'] = (new EsignController(new EsignHttpClient()))->sendDocument($eSignData);
				$this->response["error"] = $this->response['esign']["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			$this->logInfo("¿Error tras esign?" . $this->response["error"]);

			// send data to Atfinity
			if ($this->response["error"] === false) {
				$hash = $data['hash'];
				$atfinityController = new AtfinityController(new AtfinityHttpClient());
				$atfinityController->sendData($data);
			}

			$this->logInfo("generate url token");
			// generate url token
			if ($this->response["error"] === false) {
				$token = SecureToken::generateToken(100);
				$this->logInfo($token);
				$this->response["secure"] = $token;
			}

			// save data to ddbb
			if ($this->response["error"] === false) {
			}

			// get document files form Atfinity
			if ($this->response["error"] === false) {
			}

			// send Atfinity files to eSing
			if ($this->response["error"] === false) {
			}

			// get XpressID token
			if ($this->response["error"] === false) {
				// $xpressIdData = [
				// 	"language" => $data['web_language'],
				// 	"document_type" => $document_type,
				// 	"library_document_id" => $this->response['esign']['library_document_id'],
				// 	"hash" => $this->response["hash"],
				// 	"atfinity_first_name" => $data["first_name"],
				// 	"atfinity_email" => $data["email"],
				// 	"atfinity_case_id" => $this->response["atfinity"]["case_id"],
				// 	"userName" => sprintf("%s %s", $data["first_name"], $data["last_name"])
				// ];

				$this->logInfo("get XpressID token");
				$caseID = SecureToken::decodeToken($this->response["secure"]["hash"], $this->response["secure"]["key"]);

				$this->logInfo($caseID);

				$xpressIdData = [
					"language" => $data["web_language"],
					"document_type" => $document_type,
					"library_document_id" => $this->response['esign']['library_document_id'],
					"hash" => $data['hash'],
					"atfinity_first_name" => "ans",
					"atfinity_email" => "ans@example.com",
					"atfinity_case_id" => $caseId,
					"userName" => "ans arc"
				];

				$xpressIDController = new XpressIDController(new XpressIdHttpClient());
				$this->response['xpressid'] = $xpressIDController->getToken($xpressIdData);
				$this->response["error"] = $this->response['xpressid']['error'];
				if ($this->response['error']) {
					$this->logError($this->response['xpressid'], true);
				}
			}

			// Establece el endpoint para el webhook de esign según el host. En caso de error el proceso continua pero se notifica por email
			if ($this->response["error"] === false) {
			}

			// limpia el archivo para no saturar la respuesta
			$this->response["atfinity"]["files"]["file"] = "";

			$onboardingError = ($this->response["error"]) ? "true" : "false";

			$this->logInfo("[onboarding process error: " . $onboardingError . "]");
			$this->logInfo($this->response);

			$res->status(200)->toJSON([
				"data" => $this->response
			]);
			exit;
		});

		/**
		 * Verifica la validez del token especificado en el parámetro q de la url de acceso al formulario.
		 */
		Router::post('/destination=checktoken', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();
			$vars = json_decode($data["vars"], true);
			if ($vars["hash"] !== null) {
				$atfinityController = new AtfinityController(new AtfinityHttpClient());
				$this->response = $atfinityController->checkToken($vars["hash"]);
			} else {
				$this->logInfo("Hash is null or invalid: " . $vars["hash"]);
				$this->response = false;
			}
			$res->status(200)->toJSON($this->response);
			exit;
		});

		/**
		 * Proceso asíncro de cron. Busca en atfinity los casos en estado "Waiting for url", establece la url pública
		 * a enviar al cliente para el accedern al formulario con un parámetro codificado que lo relaciona con el número de caso
		 * y realiza la transición del caso a "Waiting For Client Answers"
		 */
		Router::post('/destination=seturls', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$atfinityController = new AtfinityController(new AtfinityHttpClient());
			$atfinityController->setURLS();
			$res->status(200)->toJSON([]);
			exit;
		});

		Router::get('/destination=transition', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$atfinityController = new AtfinityController(new AtfinityHttpClient());
			$atfinityController->transition(2436, 429);

			$res->toJSON([]);
			exit;
		});
	}
}
