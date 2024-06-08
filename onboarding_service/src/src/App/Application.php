<?php

namespace METRIC\App;

use METRIC\App\Controller\BaseController;
use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;

use METRIC\App\Email\Emailer;

use METRIC\App\Config\AppConfig;
use METRIC\App\Config\Constants;

use METRIC\App\Service\CheckRecaptcha;
use METRIC\App\Service\Crypto;
use METRIC\App\Service\Utils;
use METRIC\App\Service\EmailMessages;
use METRIC\App\Service\EmailProspect;

use METRIC\App\Controller\AtfinityController;
use METRIC\App\Controller\OdooController;
use METRIC\App\Controller\DBController;
use METRIC\App\Controller\EsignController;
use METRIC\App\Controller\XpressIDController;
use METRIC\App\Controller\ValidasController;
use METRIC\App\Controller\BoiDasController;


class Application extends BaseController
{

	public $request_uri;
	public $request_method;
	public $content_type;
	public $request_port;
	public $params;
	public $match;

	private $logger;
	private $emailer;
	private $countries;
	private $response = [];

	public function __construct(string $request_uri, string $request_method, string $content_type, string $request_port)
	{

		parent::__construct();

		$this->request_uri = $request_uri;
		$this->request_method = $request_method;
		$this->content_type = $content_type;

		$this->logger = new Logger;
		$this->emailer = new Emailer();

		$this->countries = Utils::getCountriesList();

		$this->response = [];

		$this->logInfo("[REQUEST:" . $request_uri . "] [method:" . $request_method . "] [port:" . $request_port . "]");

		Router::get('/live', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$res->status(200)->toText("onboarding service live...");
			exit;
		});

		/*
		 * Forzar la firma del docmento de casos en los que no se ha llegado a realizar debido a un error de esign
		 */
		Router::post('/destination=signPendingDocuments', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$this->match = "/destination=signPendingDocuments";

			$this->response["error"] = false;
			$this->response['service'] = "signPendingDocuments";

			$this->response['esign_sign_pending_documents_response'] = (new EsignController())->getSignatureRequest();

			// check error in api call
			if ($this->response['esign_sign_pending_documents_response']['error'] === true) {
				$this->logInfo("Error esign call...");
				echo $this->response['esign_sign_pending_documents_response']['error'];
				die();
			}

			$signature_request_array = json_decode($this->response['esign_sign_pending_documents_response']['info'], true)['results'];
			
			// obtener library documents id's
			$signed_library_documents_array = [];
			
			foreach ($signature_request_array as $key => $signature) {
				if ($signature['status'] === 'signed') {
					$signed_library_documents_array[] = $signature['signatures'][0]['signature_request_id'];
				}
			}

			$this->logInfo('[pending signature_request_id]: ');
			$this->logInfo($signed_library_documents_array);

			// finalizar firma
			foreach ($signed_library_documents_array as $key => $signature_request_id) {
				$this->logInfo('finish: ' . $signature_request_id);
				$response = (new EsignController())->signatureRequestFinish($signature_request_id);
				$this->logInfo('finish reponse: ' . $response);
				$this->logInfo($response);
				$this->response['esign_signature_request_finish_response'][] = $response;
			}

			echo json_encode($this->response);
			die();
		});

		// obtener el token de xpressid cuando se reinicia el proceso
		Router::post('/destination=xpressid', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();

			$this->logger->info($data);

			if (is_array($data) && count($data) > 0 && isset($data['case_id']) && filter_var($data['case_id'], FILTER_VALIDATE_INT) && isset($data['instance_id']) && filter_var($data['instance_id'], FILTER_VALIDATE_INT)) {

				$data['case_id'] = filter_var($data['case_id'], FILTER_SANITIZE_NUMBER_INT);
				$data['instance_id'] = filter_var($data['instance_id'], FILTER_SANITIZE_NUMBER_INT);

				$dataBBDD = (new DBController())->getDataWhereAtfinity($data);

				// si el caso ya está en 3, 4 ó -1 no se continua
				if ($dataBBDD['info']["state"] == Constants::STATE_BOIDAS2ATFINITY_OK || $dataBBDD['info']["state"] == Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK || $dataBBDD['info']["state"] == Constants::STATE_ESIGN_OK) {
					$dataBBDD['error'] = true;
					$this->logger->info("se ha intentado una nueva obtencion de token de xpressid del caso " . $dataBBDD['info']["atfinity_case_id"] . " en estado: " . $dataBBDD['info']["state"]);
					$this->logger->info("se interrumpe el proceso");
					$this->response["info"]["state"] = $dataBBDD['info']['state'];
					$this->response["error"] = true;
				}

				if ($dataBBDD['error'] === false) {

					$xpressdata = [
						"language" => $dataBBDD['info']["language"],
						"document_type" => $dataBBDD['info']['document_type'],
						"library_document_id" => $dataBBDD['info']['library_document_id'],
						"hash" => $dataBBDD['info']["hash"],
						"atfinity_first_name" => $data["user_name"],
						"atfinity_email" => $data["user_email"],
						"atfinity_case_id" => $data['case_id'],
						"validationId" => $dataBBDD['info']['xpressid_validation_id']
					];

					$this->response['xpressid'] = (new XpressIDController())->send($xpressdata);
					$this->response["error"] = $this->response['xpressid']["error"];

					$this->logger->info("xpressid response: ");
					$this->logger->info($this->response['xpressid']);

					// ***  cuando se solicita un nuevo token se actualiza atfinity a estado 2
					$fields['fields'][] = [
						"instance_id" => $dataBBDD['info']['atfinity_instance_fields_id'],
						"information_key" => 'boidas_status',
						"value" => 'boidas_status2'
					];
					$this->response['atfinity'] = (new AtfinityController())->update($fields, $data['case_id'], false);

					// ***  cuando se solicita un nuevo token a xpressID cambia el validation_id. Se actualiza en la bbdd y se pone estado 2
					$date = new \DateTime();
					$date = $date->format("Y-m-d H:i:s");
					$dbData = [
						"case_id" => $data['case_id'],
						"instance_id" => $data['instance_id'],
						"last_update" => $date,
						"document_type" => $dataBBDD['info']['document_type'],
						"library_document_id" => $dataBBDD['info']['library_document_id'],
						"state" => Constants::STATE_XPRESSID_UPDATE_OK, // estado 2
						"xpressid_validation_id" => $this->response['xpressid']['info']->validation_id
					];
					$this->response['db']['update_state'] = (new DBController())->update($dbData);
					$this->response["error"] = $this->response['db']['update_state']["error"];
				} else {
					//$this->logger->error($dataBBDD['info']);
				}
			} else {
				$this->response["error"] = "No data";
			}
			echo json_encode($this->response);
			die();
		});

		// proceso de onboarding
		Router::post('/destination=onboarding', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->match = "/destination=onboarding";

			$this->response["error"] = false;
			$this->response['service'] = "onboarding";

			$data = $req->getBody();



			// check recaptcha
			if ($this->response["error"] === false) {

				$this->logger->info("RECAPTCHA_CHECK: " . AppConfig::getInstance()->getValue("RECAPTCHA_CHECK"));

				if (AppConfig::getInstance()->getValue("RECAPTCHA_CHECK") != "false") {
					$this->response['recaptcha'] = CheckRecaptcha::check($data["token"], AppConfig::getInstance()->getValue("RECAPTCHA_V3_SECRET_KEY"));

					$this->response["error"] = $this->response['recaptcha']["error"];

					if ($this->response["error"] === true) {
						$this->emailer->sendMail(
							AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
							"[Onboarding] recaptcha failed with an error",
							"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response["recaptcha"]["info"]  . "</p>",
							explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
						);
						echo json_encode($this->response);
						die();
					};
				}
			}

			// get document_type
			$document_type = $this->getDocumentType($data);

			// send data to Atfinity
			if ($this->response["error"] === false) {
				$data["boidas_status"] = "boidas_status1";  // el mismo valor que se pondrá en la bbdd commo status del caso
				$this->response['atfinity'] = (new AtfinityController())->send($data);
				$this->response["error"] = $this->response['atfinity']["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			$this->logInfo($this->response);

			// obtener el instance_fields_id de Atfinity
			if ($this->response["error"] === false) {
				$instanceFieldsId = 0;
				$dataAtfinity = (new AtfinityController())->getData($this->response["atfinity"]["case_id"]);
				if ($dataAtfinity && is_object($dataAtfinity) && property_exists($dataAtfinity, 'data') && property_exists($dataAtfinity->data, 'fields') && is_array($dataAtfinity->data->fields)) {
					$arrFiltered = array_filter($dataAtfinity->data->fields, function ($fieldData) {
						return $fieldData->key === 'boidas_id';
					});
					if (count($arrFiltered) === 1) {
						$instanceFieldsId = $arrFiltered[array_key_first($arrFiltered)]->instance_id;
					}
				}

				$this->logInfo(sprintf("onboarding Atfinity instanceFieldsId: %d", $instanceFieldsId));
			}

			// send data to Odoo (En caso de que el envío de datos a Odoo falle, no se para el proceso)
			if ($this->response["error"] === false) {
				if (TESTMODE) {
					$this->response['odoo']["contact_id"] = 1000;
					$this->response["error"] = false;
				} else {
					$this->response['odoo'] = (new OdooController())->send($data);
				}
			}

			// save data to ddbb
			if ($this->response["error"] == false) {
				$date = new \DateTime();
				$date = $date->format("Y-m-d H:i:s");

				$this->response["hash"] = Crypto::hash($this->response["atfinity"]["case_id"] . "|" . $this->response["atfinity"]["instance_id"] . "|1000|1|" . $date);

				$dbData = [
					"case_id" => $this->response["atfinity"]["case_id"],
					"instance_id" => $this->response["atfinity"]["instance_id"],
					"instance_fields_id" => $instanceFieldsId,
					"odoo_id" => $this->response['odoo']["contact_id"],
					"state" => Constants::STATE_XPRESSID_OK, // estado 1 
					"language" => $data["web_language"],
					"document_type" => $document_type,
					"library_document_id" => null,
					"date" => $date,
					"last_update" => $date,
					"hash" => $this->response["hash"]
				];

				$this->response['db'] = (new DBController())->send($dbData);
				$this->response["error"] = $this->response['db']["error"];

				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			// get document files form Atfinity
			if ($this->response["error"] == false) {
				$this->response['atfinity']['files'] = (new AtfinityController())->getFiles($this->response["atfinity"]["case_id"], $data['web_language']);
				$this->response["error"] = $this->response["atfinity"]["files"]["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			// send Atfinity files to eSing
			if ($this->response["error"] == false) {
				$eSignData = [
					"file" => $this->response["atfinity"]["files"]["file"],
					"language" => $data["web_language"],
					"name" => $this->response["atfinity"]["case_id"]
				];
				$this->response['esign'] = (new EsignController())->send($eSignData);
				$this->response["error"] = $this->response['esign']["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			// get XpressID token
			if ($this->response["error"] == false) {
				$xpressdata = [
					"language" => $data['web_language'],
					"document_type" => $document_type,
					"library_document_id" => $this->response['esign']['library_document_id'],
					"hash" => $this->response["hash"],
					"atfinity_first_name" => $data["first_name"],
					"atfinity_email" => $data["email"],
					"atfinity_case_id" => $this->response["atfinity"]["case_id"],
					"userName" => sprintf("%s %s", $data["first_name"], $data["last_name"])
				];

				$this->response['xpressid'] = (new XpressIDController())->send($xpressdata);
				$this->response["error"] = $this->response['xpressid']["error"];
				// log error sending email
				if ($this->response["error"] === true) {
					$this->logError($this->response, true);
				}
			}

			// save library_document_id and xpressid_validation_id in ddbb
			if ($this->response["error"] == false) {
				$date = new \DateTime();
				$date = $date->format("Y-m-d H:i:s");
				$dbData = [
					"case_id" => $this->response["atfinity"]["case_id"],
					"instance_id" => $this->response["atfinity"]["instance_id"],
					"last_update" => $date,
					"document_type" => $document_type,
					"library_document_id" => $this->response['esign']['library_document_id'],
					"state" => Constants::STATE_XPRESSID_UPDATE_OK, // estado 2 se ha obtenido el token de xpressid
					"xpressid_validation_id" => $this->response['xpressid']['info']->validation_id
				];
				$this->response['db']['update_state'] = (new DBController())->update($dbData);
				$this->response["error"] = $this->response['db']['update_state']["error"];
			}

			// update Atfinity boidas_state field with the state value
			if ($this->response["error"] === false) {
				$fields['fields'][] = [
					"instance_id" => $instanceFieldsId,
					"information_key" => 'boidas_status',
					"value" => 'boidas_status2'
				];
				$fields['fields'][] = [
					"instance_id" => $instanceFieldsId,
					"information_key" => 'ng_hash',
					"value" => $this->response["hash"]
				];

				$this->response['atfinity'] = (new AtfinityController())->update($fields, $this->response["atfinity"]["case_id"], false);
			}

			// Establece el endpoint para el webhook de esign según el host en caso de error el proceso continua pero se notifica por email
			if ($this->response["error"] === false) {

				$this->response['esign_webhook'] = (new EsignController())->setWebHookUrl(AppConfig::getInstance()->getValue("ESIGN_WEBHOOK_URL"));

				$response = json_decode($this->response['esign_webhook']["info"]);

				$this->response["error"] = $this->response['esign_webhook']["error"];

				if ($this->response['esign_webhook']["error"] || ($response->webhook_url != AppConfig::getInstance()->getValue("ESIGN_WEBHOOK_URL"))) {
					$this->logger->error("[set esign webhook: no se ha podido establecer el webhook]");
					$this->emailer->sendMail(
						AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
						"[Onboarding] set esign webhook failed with an error",
						"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response['esign_webhook']["info"]  . "</p>",
						explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
					);
				} else {
					$this->logger->info("[set esign webhook: ok]");
				}
			}

			// limpia el archivo para no saturar la respuesta
			$this->response["atfinity"]["files"]["file"] = "";

			$onboardingError = ($this->response["error"]) ? "true" : "false";

			$this->logger->info("[onboarding process error: " . $onboardingError . "]");

			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		// proceso de onboarding cuando se hace click en el enlace de terageting
		Router::post('/destination=updateCase', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->match = "/destination=updateCase";

			$this->response["error"] = false;
			$this->response['service'] = "updateCase";

			$data = $req->getBody();

			$this->logger->info("[data request:]");
			$this->logger->info($data);

			// check recaptcha
			if ($this->response["error"] === false) {
				if (AppConfig::getInstance()->getValue("RECAPTCHA_CHECK") != "false") {
					$this->response['recaptcha'] = CheckRecaptcha::check($data["token"], AppConfig::getInstance()->getValue("RECAPTCHA_V3_SECRET_KEY"));

					$this->response["error"] = $this->response['recaptcha']["error"];

					if ($this->response["error"] === true) {
						$this->emailer->sendMail(
							AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
							"[Onboarding] recaptcha failed with an error",
							"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response["recaptcha"]["info"]  . "</p>",
							explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
						);
						echo json_encode($this->response);
						die();
					};
				}
			}

			// leer la información del caso de la bbdd
			if ($this->response["error"] === false) {
				$this->response['db'] = (new DBController())->read($data);
				$this->response["error"] = $this->response['db']["error"];
			}

			$this->logInfo("[case state: " . $this->response['db']['info']['state'] . " ]");

			// si el caso ya está en 3, 4 ó -1 no se continua
			if ($this->response['db']["info"]["state"] == Constants::STATE_BOIDAS2ATFINITY_OK || $this->response['db']["info"]["state"] == Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK || $this->response['db']["info"]["state"] == Constants::STATE_ESIGN_OK) {
				$this->response["error"] = true;
				$this->logger->info("se ha intentado una nueva operación de retargeting del caso " . $this->response['db']["info"]["atfinity_case_id"] . " en estado: " . $this->response['db']["info"]["state"]);
				$this->logger->info("se interrumpe el proceso");
			}

			// update data (country and document) to Atfinity
			if ($this->response["error"] === false) {
				$data['db']['atfinity_case_id'] = $this->response['db']['info']['atfinity_case_id'];
				$data['db']['atfinity_instance_id'] = $this->response['db']['info']['atfinity_instance_id'];
				$data['db']['odoo_id'] = $this->response['db']['info']['odoo_id'];

				// actualizar la información en Atfinity
				$this->response['atfinity'] = (new AtfinityController())->update($data);
				$this->response["error"] = $this->response['atfinity']["error"];
			}

			// send data to Odoo
			if ($this->response["error"] === false) {
				$this->response['odoo'] = (new OdooController())->update($data);
				$this->response["error"] = $this->response['odoo']["error"];
			}

			// get document files form Atfinity
			if ($this->response["error"] == false) {
				$this->response['atfinity']['files'] = (new AtfinityController())->getFiles($this->response["atfinity"]["case_id"], $data['web_language']);
				$this->response["error"] = $this->response["atfinity"]["files"]["error"];
			}

			// send Atfinity files to eSing
			if ($this->response["error"] == false) {
				$eSignData = [
					"file" => $this->response["atfinity"]["files"]["file"],
					"language" => $data["web_language"],
					"name" => $this->response["atfinity"]["case_id"]
				];
				$this->response['esign'] = (new EsignController())->send($eSignData);
				$this->response["error"] = $this->response['esign']["error"];
			}

			// obtener información de Atfinity sobre el caso para pasar a XpressID
			if ($this->response["error"] === false) {
				$this->response["atfinity"] = $this->getAtfinityCaseById($this->response['db']['info']['atfinity_case_id']);
				$this->response["error"] = $this->response["atfinity"]["error"];
			}

			// get XpressID token


			if ($this->response["error"] == false) {
				$document_type = $this->getDocumentType($data);
				$xpressdata = [
					"language" => $data['web_language'],
					"document_type" => $document_type,
					"library_document_id" => $this->response['esign']['library_document_id'],
					"hash" => $data["hash"],
					"atfinity_first_name" => $this->response["atfinity"]["atfinity_case_first_name"],
					"atfinity_email" => $this->response["atfinity"]["atfinity_case_email"],
					"atfinity_case_id" => $this->response['db']['info']['atfinity_case_id'],
					"email" => (isset($data["email"]) && !empty($data["email"])) ? $data["email"] : '',
					"userName" => (isset($data["first_name"]) && isset($data["last_name"]) && !empty($data["first_name"]) && !empty($data["last_name"])) ? sprintf("%s %s", $data["first_name"], $data["last_name"]) : ''
				];

				$this->response['xpressid'] = (new XpressIDController())->send($xpressdata);
				$this->response["error"] = $this->response['xpressid']["error"];
			}

			// update state ddbb
			if ($this->response["error"] == false) {
				$date = new \DateTime();
				$date = $date->format("Y-m-d H:i:s");
				$dbData = [
					"case_id" => $this->response['db']['info']["atfinity_case_id"],
					"instance_id" => $this->response['db']['info']["atfinity_instance_id"],
					"last_update" => $date,
					"document_type" => $document_type,
					"state" => Constants::STATE_XPRESSID_UPDATE_OK, // estado 1,2,3 datos personael, retargeting, ...
					"library_document_id" => $this->response['esign']['library_document_id'],
					"xpressid_validation_id" => $this->response['xpressid']['info']->validation_id
				];
				$this->response['db']['info']['xpressid_validation_id'] = $this->response['xpressid']['info']->validation_id;
				$this->response['db']['update_state'] = (new DBController())->update($dbData);
				$this->response["error"] = $this->response['db']['update_state']["error"];
			}

			// reset count_error in ddbb
			if ($this->response["error"] == false) {
				$dbData = [
					"case_id" => $this->response['db']['info']["atfinity_case_id"],
					"instance_id" => $this->response['db']['info']["atfinity_instance_id"],
				];
				$this->response['db']['reset_count_error'] = (new DBController())->resetCountError($dbData);
				$this->response["error"] = $this->response['db']['reset_count_error']["error"];
			}

			// actualiza el campo boidas_to_atfinity a false para que pueda volver a ser tratado en el proceso de descarga de datos de boidas a Atfinity (Boidas2Atfinity). Es susceptible de mejora, actualizando solo el campo en cuestion
			if ($this->response["error"] == false) {
				$date = new \DateTime();
				$date = $date->format("Y-m-d H:i:s");
				$dbData = [
					"case_id" => $this->response['db']['info']["atfinity_case_id"],
					"instance_id" => $this->response['db']['info']["atfinity_instance_id"],
					"last_update" => $date,
					"webhook_timestamp" => $webhook_timestamp,
					"state" => Constants::STATE_XPRESSID_UPDATE_OK,
					"boidas_to_atfinity" => false
				];
				$this->response['db']['update_state'] = (new DBController())->updateStateBoidasToAtfinity($dbData);

				// Detener proceso de retargeting actualiza la tabla onboarding a retargeting -1
				$dbData = [
					"counter" => -1, // contador de envíos de email
					"onboarding_id" => $this->response['db']['info']["id"],
					"atfinity_case_id" => $this->response['db']['info']["atfinity_case_id"]
				];
				(new DBController())->setCounterField($dbData);

				// actualiza la tabla onboarding
				$dbData = [
					"retargeting" => 0, // ya no se realizan más operaciones de retargeting sobre el caso
					"atfinity_case_id" => $this->response['db']['info']["atfinity_case_id"],
					"atfinity_instance_id" => $this->response['db']['info']["atfinity_instance_id"]
				];
				(new DBController())->updateRetargetingField($dbData);
			}

			// Establece el endpoint para el webhook de esign según el host en caso de error el proceso continua pero se notifica por email
			if ($this->response["error"] === false) {

				$this->response['esign_webhook'] = (new EsignController())->setWebHookUrl(AppConfig::getInstance()->getValue("ESIGN_WEBHOOK_URL"));

				$response = json_decode($this->response['esign_webhook']["info"]);

				if ($this->response['esign_webhook']["error"] || ($response->webhook_url != AppConfig::getInstance()->getValue("ESIGN_WEBHOOK_URL"))) {
					$this->logger->error("[set esign webhook: no se ha podido establecer el webhook]");
					$this->emailer->sendMail(
						AppConfig::getInstance()->getValue("EMAIL_SERVICE_TO"),
						"[Onboarding] set esign webhook failed with an error",
						"<p>For your attention,</p><p>Error, recaptcha </p><p>" . $this->response['esign_webhook']["info"]  . "</p>",
						explode(',', AppConfig::getInstance()->getValue("EMAIL_SERVICE_CC"))
					);
				} else {
					$this->logger->info("[set esign webhook: ok]");
				}
			}

			// limpia el archivo para no saturar la respuesta
			if ($this->response["error"] === false) {
				$this->response["atfinity"]["files"]["file"] = "";
			}

			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		/*
		 * Tarea de cron encargada de llevar la información de la biometría de boidas a atfinity  
		 */
		Router::get('/destination=boidas2atfinity', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$response = [];

			// obtiene los registros en estado (3, -3, -1) y con el campo webhook_timestamp < now (hora en que se recibe el evento de firmado o no + 2 minutos para que la información llegue a boidas) y el campo boidas_to_atfinity = 0 (no se ha pasado aún la información de este caso a Atfinity). También se pasa la información de los casos en estado -1 por low score.
			$dataBBDD = (new DBController())->getDataWhereState(['state' => [Constants::STATE_ESIGN_OK, Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK, Constants::STATE_ESIGN_REFUSE]]);

			$this->logInfo($dataBBDD);

			if ($dataBBDD['error'] === false) {
				$rows = $dataBBDD['info'];
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$this->logInfo($row);
						$data = array(
							'validation_id' => $row['xpressid_validation_id'],
							'case_id' => $row['atfinity_case_id'],
							'instance_id' => $row['atfinity_instance_id'],
							'webhook_timestamp' => $row['webhook_timestamp'],
							'atfinity_instance_fields_id' => $row['atfinity_instance_fields_id'],
							'state' => $row['state']
						);
						$ret = $this->boidas2Atfinity($data);
						$responseStr = sprintf(
							"case_id: %d ; instance_id: %d ; validation_id: %s ; boidas2Atfinity completed: %s",
							$data['case_id'],
							$data['instance_id'],
							$data['validation_id'],
							($ret) ? "true" : "false"
						);
						$response[] = $responseStr;
						$this->logInfo($responseStr);
					}
				}
			}

			$this->logger->info("[boidas2Atfinity process finished: ]");

			$res->toJSON([
				"response" => $response
			]);
			exit;
		});

		/*
		 * Obtiene un nuevo tokend e XpressID y lo presenta en pantalla cuando se scanea el QR presentado en la pantalla del ordenador y se redirige al móvil.
		 */
		Router::post('/destination=mobileRedirect', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$this->match = "/destination=mobileRedirect";

			$this->response["error"] = false;
			$this->response['service'] = "mobileRedirect";

			$data = $req->getBody();

			$this->logInfo("mobileRedirect....");
			$this->logInfo($data);

			// leer la información del caso de la bbdd
			if ($this->response["error"] === false) {
				$this->response['db'] = (new DBController())->read($data);
				$this->response["error"] = $this->response['db']["error"];
			}

			// si el caso ya está en 3, 4 ó -1 no se continua
			if ($this->response["error"] === false) {
				if ($this->response['db']["info"]["state"] == Constants::STATE_BOIDAS2ATFINITY_OK || $this->response['db']["info"]["state"] == Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK || $this->response['db']["info"]["state"] == Constants::STATE_ESIGN_OK) {
					$this->response["error"] = true;
					$this->logger->info("se ha realizado un mobileRedirect del caso " . $this->response['db']["info"]["atfinity_case_id"] . " en estado: " . $this->response['db']["info"]["state"]);
					$this->logger->info("se interrumpe el proceso");
				}
			}

			// obtener información de Atfinity sobre el caso para pasar a XpressID
			if ($this->response["error"] === false) {
				$this->response["atfinity"] = $this->getAtfinityCaseById($this->response['db']["info"]["atfinity_case_id"]);
				$this->response["error"] = $this->response["atfinity"]["error"];
			}

			// get XpressID token
			if ($this->response["error"] == false) {
				$xpressdata = [
					"language" => $this->response['db']['info']['language'],
					"document_type" => $this->response['db']['info']['document_type'],
					"library_document_id" => $this->response['db']['info']['library_document_id'],
					"hash" => $data["hash"],
					"atfinity_first_name" => $this->response["atfinity"]["atfinity_case_first_name"],
					"atfinity_email" => $this->response["atfinity"]["atfinity_case_email"],
					"atfinity_case_id" => $this->response['db']["info"]["atfinity_case_id"]
				];

				$this->response['xpressid'] = (new XpressIDController())->send($xpressdata);
				$this->response["error"] = $this->response['xpressid']["error"];

				$this->logger->info("xpressid response: ");
				$this->logger->info($this->response['xpressid']['info']->validation_id);
			}

			// update ddbb
			if ($this->response["error"] == false) {
				$date = new \DateTime();
				$date = $date->format("Y-m-d H:i:s");
				$dbData = [
					"case_id" => $this->response['db']['info']["atfinity_case_id"],
					"instance_id" => $this->response['db']['info']["atfinity_instance_id"],
					"last_update" => $date,
					"document_type" => $this->response['db']['info']['document_type'],
					"state" => Constants::STATE_XPRESSID_UPDATE_OK, // estado 2
					"library_document_id" => $this->response['db']['info']['library_document_id'],
					"xpressid_validation_id" => $this->response['xpressid']['info']->validation_id
				];
				$this->response['db']['update_state'] = (new DBController())->update($dbData);
				$this->response["error"] = $this->response['db']['update_state']["error"];
				$this->logInfo($this->response['db']["info"]["atfinity_case_id"]);
			}

			// update Atfinity boidas_state field with the state value
			if ($this->response["error"] === false) {
				$fields['fields'][] = [
					"instance_id" => $this->response['db']['info']['atfinity_instance_fields_id'],
					"information_key" => 'boidas_status',
					"value" => 'boidas_status2'
				];

				$this->response['atfinity'] = (new AtfinityController())->update($fields, $this->response['db']["info"]["atfinity_case_id"], false);
			}

			$res->status(200)->toJSON([
				"origin" => "/destination=mobileRedirect",
				"data" => $this->response
			]);
		});

		/*
		 * Endpoint al que llama eSign cuando la firma del documento se realiza o se declina. El endpoint captura el evento y modifica el estado de Atfinity
		 * y de la bbdd a 3 ó -3
		 */
		Router::post('/destination=esignWebhook', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();

			$data[0] = stripslashes($data[0]);

			$this->response["error"] = false;

			// capture event and download zip file
			$this->response['esignWebhook'] = (new EsignController())->webHook($data);

			$this->logInfo("event_time: " . $this->response['esignWebhook']["event_time"]);

			$res->status(202)->toText(); // respuesta prematura al servidor para evitar timeout por su parte

			if ($this->response['esignWebhook']['error'] === false && array_key_exists("signature_request_id", $this->response['esignWebhook'])) {


				if ($this->response['esignWebhook']["event_type"] === 'signature_signed') {
					/*
					$dbData = [
						"validation_id" => $this->response['esignWebhook']["validation_id"],
						"signature_request_id" => $this->response['esignWebhook']["signature_request_id"]
					  ];
			
					  //actualiza signature_request_id en el registro de la tabla onboarding
					  (new DBController())->updateSignatureRequestId($dbData);
					*/
					exit;
				}

				// obtener el case_id de la bbdd
				$this->response['db'] = (new DBController())->getDataWhereSignatureRequestId(["signature_request_id" => $this->response['esignWebhook']["signature_request_id"]]);
				$this->response["error"] = $this->response['db']["error"];

				$this->logInfo('[case_id: ' . $this->response['db']['info']['atfinity_case_id'] . ']');

				$caseId = $this->response['db']['info']['atfinity_case_id'];
				$instanceId = $this->response['db']['info']['atfinity_instance_id'];
				$instanceFieldsId = $this->response['db']['info']['atfinity_instance_fields_id'];

				if ($this->response['esignWebhook']["event_type"] === 'signature_request_timestamped') {

					// recoger los proofs ids de los ficheros a subir
					$responseProof = (new AtfinityController())->getProofIds($caseId);
					$dataProofIds = [];
					if ($responseProof && !$responseProof['error']) {
						$dataProofIds = $responseProof['data'];
					}
					$appConfig = AppConfig::getInstance();
					$proofId = (isset($dataProofIds[$appConfig->getValue("PROOF_ID_NG_SIGNED_DOCUMENT")])) ? $dataProofIds[$appConfig->getValue("PROOF_ID_NG_SIGNED_DOCUMENT")] : 0;

					// upload file to Atfinity
					if ($proofId > 0) {
						$this->response['esignWebhook']['uploaded'] = (new AtfinityController())->uploadFileWithId($caseId, $proofId, "signature_documents.zip", $this->response['esignWebhook']['file'], false);
					} else {
						$this->response['esignWebhook']['uploaded'] = (new AtfinityController())->uploadFile($caseId, "signature_documents.zip", $this->response['esignWebhook']['file'], false);
					}

					// update Atfinity field boidas_status to 3
					$fields['fields'][] = [
						"instance_id" => $instanceFieldsId,
						"information_key" => 'boidas_status',
						"value" => 'boidas_status3'
					];

					$this->response['atfinity'] = (new AtfinityController())->update($fields, $caseId, false);
					$state = Constants::STATE_ESIGN_OK;

					$this->logInfo('[esignWebHook signature_signed state: ' . $state . ']');
				} else if ($this->response['esignWebhook']["event_type"] === 'signature_declined') {

					// update Atfinity field boidas_status to -3
					$fields['fields'][] = [
						"instance_id" => $instanceFieldsId,
						"information_key" => 'boidas_status',
						"value" => 'boidas_statusminus3'
					];

					$this->response['atfinity'] = (new AtfinityController())->update($fields, $caseId, false);

					$state = Constants::STATE_ESIGN_REFUSE;

					$this->logInfo('[esignWebHook signature_declined state: ' . $state . ']');
				}

				// actualiza state a 3 ó -3 (Si se ha recibido el evento de documento firmado 3, o de rechazo -3)
				if ($state == Constants::STATE_ESIGN_OK || $state == Constants::STATE_ESIGN_REFUSE) {
					$date = new \DateTime();
					$date = $date->format("Y-m-d H:i:s");
					$dbData = [
						"case_id" => $caseId,
						"instance_id" => $instanceId,
						"last_update" => $date,
						"webhook_timestamp" => $this->response['esignWebhook']["event_time"] + 120,  // save event time + 120 sec. for update Atfinity with boidas data.
						"state" => $state, // estado 1,2,3 datos personals, retargeting, ...
					];

					$this->response['db']['update_state'] = (new DBController())->updateState($dbData);
				}
			}

			exit;
		});

		/*
		 * Endpoint al que se llama cuando XpressID produce un evento tipo "ProcessError". Confirma el caso en validas dejándolo disponible para boidas.
		 * Tambien verifica los scores global y de documentos.
		 */
		Router::post('/destination=validas', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();

			if (
				is_array($data)
				&& count($data) > 0
				&& isset($data['case_id'])
				&& filter_var($data['case_id'], FILTER_VALIDATE_INT)
				&& isset($data['instance_id'])
				&& filter_var($data['instance_id'], FILTER_VALIDATE_INT)
			) {

				$caseId = filter_var($data['case_id'], FILTER_SANITIZE_NUMBER_INT);
				$instanceId = filter_var($data['instance_id'], FILTER_SANITIZE_NUMBER_INT);

				$dbData = [
					"case_id" => $caseId,
					"instance_id" => $instanceId
				];
				$count_error = 0;
				$dataBBDD = (new DBController())->getDataWhereAtfinity($dbData);
				if ($dataBBDD['error'] === false) {
					$validationId = $dataBBDD['info']['xpressid_validation_id'];
					$data["validation_id"] = $validationId;
					$count_error = $dataBBDD['info']['count_error'];
					$instanceFieldsId = $dataBBDD['info']['atfinity_instance_fields_id'];
				}

				$this->logInfo(sprintf("caseId: %d , instanceId: %d , validationId: %s", $caseId, $instanceId, $validationId));

				// si el caso ya esta en 3, 4 ó -1 no se hace nada
				if ($dataBBDD['info']['state'] != 3 && $dataBBDD['info']['state'] != 4 && $dataBBDD['info']['state'] != -1) {
					// confirma y finaliza la validación, de modo que estarán disponibles para que boidas tome los datos, solo se confirman las validaciones
					// con almenos el análisis de la imagen del anverso del documento corectamente realizada.
					$this->response['confirm'] = (new ValidasController())->confirm($data);

					$this->logInfo("[validas confirm]");
					$this->logInfo($this->response);

					$date = new \DateTime();

					// update data error
					if (isset($data['data_error'])) {
						$data_error = filter_var($data['data_error'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
						$dbData = [
							"case_id" => $caseId,
							"instance_id" => $instanceId,
							"last_update" => $date,
							'data_error' => $data_error
						];
						$this->response['db']['data_error'] = (new DBController())->updateDataError($dbData);
					}

					// confirmation ok
					if ($this->response['confirm']['httpcode'] === 204) {

						// obtenemos los datos de la validacion por si el validationGlobalScore es < 0.5 mostrar mensaje de error al usuario
						$this->response['validas'] = (new ValidasController())->getResults($data);

						$validationGlobalScore = $this->response['validas']['info']->data->data->summary->scores[0]->value;

						$this->response['validas']['validationGlobalScore'] = $validationGlobalScore;

						$this->logInfo("[validationGlobalScore: " . $validationGlobalScore . "]");

						if ($validationGlobalScore < 0.5) {
							$recordState = Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK;
							$atfinityState = "boidas_statusminus1";
						} else {

							// comprobamos los scores que indican que no son fotocopias
							$boidas_scoredoc1 = round($this->getValueFromArray($this->response['validas']['info']->data->data->document->scores, "ScoreGroup-PhotoAuthenticity"), 3);
							$boidas_scoredoc2 = round($this->getValueFromArray($this->response['validas']['info']->data->data->document->scores, "ScoreGroup-PrintAttackTest"), 3);
							$boidas_scoredoc3 = round($this->getValueFromArray($this->response['validas']['info']->data->data->document->scores, "ScoreGroup-ReplayAttackTest"), 3);

							// si los scores de documento > 70% estado a -4 para hacer retargeting
							if ($boidas_scoredoc1 > 0.7 && $boidas_scoredoc2 > 0.7 && $boidas_scoredoc3 > 0.7) {
								$recordState = Constants::STATE_ESIGN_LOW_SCORE;
								$atfinityState = "boidas_statusminus4";
							} else {
								$recordState = Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK;
								$atfinityState = "boidas_statusminus1";
							}
						}

						// update Atfinity field boidas_status
						$fields['fields'][] = [
							"instance_id" => $instanceFieldsId,
							"information_key" => 'boidas_status',
							"value" => $atfinityState
						];

						$this->response['atfinity'] = (new AtfinityController())->update($fields, $caseId, false);

						// increase error counter
						$dbData = [
							"case_id" => $caseId,
							"instance_id" => $instanceId,
							"last_update" => $date
						];
						$this->response['db']['count_error'] = (new DBController())->updateCountError($dbData);
						$this->response['db']['count_error']['count'] = ++$count_error;

						// update state to in db
						$dateStr = $date->format("Y-m-d H:i:s");
						$d2 = $date->getTimestamp() + 120;
						$dbData = [
							"case_id" => $caseId,
							"instance_id" => $instanceId,
							"last_update" => $dateStr,
							"state" => $recordState,
							'webhook_timestamp' => $d2
						];
						$this->response['db']['update_state'] = (new DBController())->updateState($dbData);
						$this->response['case_state'] = $recordState;

						// Atfinity transition, si el caso queda en estado -1
						if ($recordState == Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK) {

							$transitionId = 227;

							$this->response['transition'] = (new AtfinityController())->stateTransition($caseId, $transitionId);

							if ($this->response['transition']['error'] === false) {
								$resp = json_decode($this->response['transition']["info"]);
								if (is_object($resp)) {
									if (property_exists($resp, "data")) {
										if (property_exists($resp->data, "id")) {
											$this->logInfo("case " . $resp->data->id . " -> transition: " . $transitionId . ": ok");
										}
									}
								} else {
									$this->logInfo("case " . $caseId . " -> transition: " . $transitionId . ": fail");
									$this->logInfo($resp);
								}
							}
						}
					}

					// error en la confirmación en validas
					if ($this->response['confirm']['httpcode'] !== 204) {


						// update Atfinity field boidas_status -2
						$fields['fields'][] = [
							"instance_id" => $instanceFieldsId,
							"information_key" => 'boidas_status',
							"value" => 'boidas_statusminus2'
						];

						$this->response['atfinity'] = (new AtfinityController())->update($fields, $caseId, false);

						// increase error counter in ddbb
						$dbData = [
							"case_id" => $caseId,
							"instance_id" => $instanceId,
							"last_update" => $date
						];
						$this->response['db']['count_error'] = (new DBController())->updateCountError($dbData);
						$this->response['db']['count_error']['count'] = ++$count_error;

						// update state to -2 in db
						$dateStr = $date->format("Y-m-d H:i:s");
						$d2 = $date->getTimestamp() + 120;
						$dbData = [
							"case_id" => $caseId,
							"instance_id" => $instanceId,
							"last_update" => $dateStr,
							"state" => Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_KO,
							'webhook_timestamp' => $d2
						];
						$this->response['db']['update_state'] = (new DBController())->updateState($dbData);
						$this->response['case_state'] = Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_KO;
					}
				} else {
					$this->logInfo("[validas no confirm case " . $data['case_id'] . " in state: " . $dataBBDD['info']['state'] . "]");
					$this->response['confirm']['httpcode'] = "no comfirm";
				}

				$res->status(200)->toJSON([
					"data" => $this->response
				]);

				exit;
			}
		});

		/*
		 * Obtiene el estado de un caso. Este endpoint es llamado desde el frontend para sincronizar la pantalla tanto del ord. como del movil con el estado del caso.
		 */
		Router::post('/destination=state', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$data = $req->getBody();

			$this->logInfo('[data:]');
			$this->logInfo($data);

			if (is_array($data) && count($data) > 0) {

				$dataParams = $data;
				$dataBBDD = null;

				if (
					array_key_exists('case_id', $dataParams)
					&& filter_var($dataParams['case_id'], FILTER_VALIDATE_INT)
					&& array_key_exists('instance_id', $dataParams)
					&& filter_var($dataParams['instance_id'], FILTER_VALIDATE_INT)
				) {

					$caseId = filter_var($dataParams['case_id'], FILTER_SANITIZE_NUMBER_INT);
					$instanceId = filter_var($dataParams['instance_id'], FILTER_SANITIZE_NUMBER_INT);
					$action = (array_key_exists('action', $dataParams)) ? filter_var($dataParams['action'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

					$dbData = [
						"case_id" => $caseId,
						"instance_id" => $instanceId
					];
					$dataBBDD = (new DBController())->getDataWhereAtfinity($dbData);

					// $this->logInfo("[dataBBDD]");
					// $this->logInfo($dataBBDD);

				} else if (array_key_exists('validation_id', $dataParams)) {

					$validationId = filter_var($dataParams['validation_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					$dbData = [
						"validation_id" => $validationId
					];
					$dataBBDD = (new DBController())->getDataWhereValidationId($dbData);
				}

				if (!is_null($dataBBDD)) {
					if ($dataBBDD['error'] === false) {
						$this->response['state'] = $dataBBDD['info']['state'];
					}
				}

				//******************************

				// comprobar si ha llegado la fecha para el envío. Si está en modo test no comprueba la fecha (para checkear emails)

				$this->logInfo("Checking in DB last_update: " . $dataBBDD['info']['last_update']);
				$time = new \DateTime($dataBBDD['info']['last_update']);
				$time->add(new \DateInterval('PT16M'));
				$this->logInfo("Inactivity time: " . $time->format("Y-m-d H:i:s"));
				$isInactivity = $this->checkIfSend($time->format("Y-m-d H:i:s"));
				$this->logInfo("Comprobando si ha llegado la hora de actualizar por inactividad....");
				$message = ($isInactivity) ? "true" : "false";
				$message = "es la hora: " . $message;
				$this->logInfo($message);

				//********************************

				// si es por inactividad y el estado es 2 y hace más de 16min. desde la última actualización del caso. Actualiza a estado -5
				if ($action == 'inactivity' && $dataBBDD['info']['state'] == Constants::STATE_XPRESSID_UPDATE_OK && $isInactivity) {
					$this->logInfo("inactivity: " . $action);

					// update state in db
					$date = new \DateTime();
					$date = $date->format("Y-m-d H:i:s");
					$dbData = [
						"case_id" => $caseId,
						"instance_id" => $instanceId,
						"last_update" => $date,
						"webhook_timestamp" => $dataBBDD['info']['webhook_timestamp'],
						"state" => Constants::STATE_ESIGN_TIMEOUT_INACTIVITY
					];
					$response = (new DBController())->updateState($dbData);

					$this->logInfo("[response:]");
					$this->logInfo($response);

					// update atfinity
					$fields['fields'][] = [
						"instance_id" => $dataBBDD['info']['atfinity_instance_fields_id'],
						"information_key" => 'boidas_status',
						"value" => "boidas_statusminus5"
					];
					$response = (new AtfinityController())->update($fields, $caseId, false);

					$this->logInfo("[response:]");
					$this->logInfo($response);

					$this->response['state'] = Constants::STATE_ESIGN_TIMEOUT_INACTIVITY;
				}
			}

			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		/*
		 * Tarea de cron encargada de enviar emails a los casos de retargeting
		 * data: test == 1 // fuerza el envío de emails sin comprobar fechas para testear los correos
		 * data: email_test == example@example.com // email al que se envía las pruebas
		 * data: host == prod // obtiene las imágenes de los email de bbva.ch
		 */
		Router::post('/destination=retargeting', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$data = $req->getBody();

			$this->logInfo($data);

			$data["test"] = (array_key_exists("test", $data)) ? $data["test"] : 0;
			$data["email_test"] = (array_key_exists("email_test", $data)) ? $data["email_test"] : "";

			
			// obtiene casos de la tabla onboarding que no son (4, 3, -1, -4 y retargeting > -1)
			$this->response['db']['cases'] = (new DBController())->getRetargetingCasesFromOnboarding();

			$toSend = [];

			foreach ($this->response['db']['cases']['info'] as $key => $case) {

				if ($case["retargeting"] == 1) {

					// registros que ya están en la tabla retargeting
					$dbData = [
						"onboarding_id" => $case["id"],
						"atfinity_case_id" => $case["atfinity_case_id"]
					];

					// obtener el caso de la tabla retargeting
					$retargetingCase = (new DBController())->getRetargetingByOnboardingIdAndCaseId($dbData);

					$this->logInfo("retargeting case:");
					$this->logInfo($retargetingCase);

					if (count($retargetingCase["info"]) > 0) {
						$this->logInfo("db retargeting case...");
						$this->logInfo($retargetingCase["info"]);
					}


					if (!empty($retargetingCase["info"])) {

						// comprobar si ha llegado la fecha para el envío. Si está en modo test no comprueba la fecha (para checkear emails)
						$isTheHour = ($data["test"] == 0) ? $this->checkIfSend($retargetingCase["info"][0]["next_update"]) : true;

						if ($isTheHour == true) {

							$this->logInfo("isTheHour: " . $isTheHour);

							$case["counter"] = $retargetingCase["info"][0]["counter"];
							$case["retargeting_id"] = $retargetingCase["info"][0]["id"];

							// actualiza la tabla retargeting pone el contador del caso en -1 (ya no se envian más email)
							if ($case["counter"] == 5) {
								$dbData = [
									"counter" => -1, // contador de envíos de email
									"onboarding_id" => $case["id"],
									"atfinity_case_id" => $case["atfinity_case_id"]
								];
								(new DBController())->setCounterField($dbData);

								// actualiza la tabla onboarding
								$dbData = [
									"retargeting" => -1, // ya no se realizan más operaciones de retargeting sobre el caso
									"atfinity_case_id" => $case["atfinity_case_id"],
									"atfinity_instance_id" => $case["atfinity_instance_id"]
								];
								(new DBController())->updateRetargetingField($dbData);

								// Atfinity transition
								//$transitionId = 227;
								$transitionId = AppConfig::getInstance()->getValue("ATFINITY_STATUS_MK_FAILED");
								$this->logInfo("ATFINITY_STATUS_MK_FAILED transitionId: " . $transitionId);

								$this->response['transition'] = (new AtfinityController())->stateTransition($case["atfinity_case_id"], $transitionId);

								if ($this->response['transition']['error'] === false) {
									$resp = json_decode($this->response['transition']["info"]);
									if (is_object($resp)) {
										if (property_exists($resp, "data")) {
											if (property_exists($resp->data, "id")) {
												$this->logInfo("case " . $resp->data->id . " -> transition: " . $transitionId . ": ok");
											}
										}
									} else {
										$this->logInfo("case " . $caseId . " -> transition: " . $transitionId . ": fail");
										$this->logInfo($resp);
									}
								}
							} else {
								$toSend[] = $case;
							}
						}
					}
				} else {
					// registros nuevos

					// si es caso 1 ó 2 verificar el tiempo por si el usuario está en proceso de relleno del formulario y estar seguros
					// de que ha abandonado después de rellenar el formulario.
					if ($case['state'] == 1 || $case['state'] == 2) {
						$this->logInfo("es caso 1 ó 2, se comprueba la fecha de alta del registro en la bbdd");
						$time = new \DateTime($case["last_update"]);
						$time->add(new \DateInterval('PT30M'));

						// comprobar si ha llegado la fecha para el envío. Si está en modo test no comprueba la fecha (para checkear emails)
						$isTheHour = ($data["test"] == 0) ? $this->checkIfSend($time->format("Y-m-d H:i:s")) : true;
						//$isTheHour = $this->checkIfSend($time->format("Y-m-d H:i:s"));

						$this->logInfo("Comprobando si ha llegado la hora de pasar un caso 1 ó 2  a la tabla de retargeting....");
						$this->logInfo($time);
						$this->logInfo($case['date']);
					} else {
						$isTheHour = true;
					}

					if ($isTheHour == true) {
						$this->logInfo("ha llegado la hora de pasar un caso 1 ó 2  a la tabla de retargeting....");

						// salva el caso en tabla retargeting
						$this->response['db']['save'] = (new DBController())->saveCaseInRetargetingTable($case);

						$this->logInfo("db saved case in retargeting table...");
						$this->logInfo($this->response['db']['save']);

						if ($this->response['db']['save']) {

							$dbData = [
								"retargeting" => 1, // el caso está en la tabla de retargeting
								"atfinity_case_id" => $case["atfinity_case_id"],
								"atfinity_instance_id" => $case["atfinity_instance_id"]
							];

							// actualiza el campo retargeting = 1 en la tabla onboardig
							$this->response['db']['update'] = (new DBController())->updateRetargetingField($dbData);
						}
					} else {
						$this->logInfo("NO ha llegado la hora de pasar un caso 1  2  a la tabla de retargeting....");
					}
				}
			}

			// obtener email y campo boidas_status de Atfinity
			foreach ($toSend as $key => $case) {
				$atfinityCase = (new AtfinityController())->read(["atfinity_case_id" => $case["atfinity_case_id"]]);
				if ($atfinityCase["error"] === false) {
					$atfinityCase = json_decode($atfinityCase["info"]);
					if (property_exists($atfinityCase, 'data')) {
						if (property_exists($atfinityCase->data, 'fields')) {
							foreach ($atfinityCase->data->fields as $key2 => $value) {
								if ($value->key == "email") {
									$toSend[$key]["email"] = $value->value;
								}
								if ($value->key == "boidas_status") {
									$toSend[$key]["boidas_status"] = $value->value->key;
								}
								if ($value->key == "first_name") {
									$toSend[$key]["first_name"] = $value->value;
								}
							}
						} else {
							unset($toSend[$key]);
						}
					} else {
						unset($toSend[$key]);
					}
				} else {
					unset($toSend[$key]);
				}
			}

			// filtrar por boidas status
			foreach ($toSend as $key => $value) {
				if ($value["boidas_status"] == "boidas_status4" || $value["boidas_status"] == "boidas_status3" || $value["boidas_status"] == "boidas_statusminus1") {
					unset($toSend[$key]);
				}
			}

			//********* send emails, update counter and last_update, field in retargeting table, Atfinity transition.

			foreach ($toSend as $key => $case) {

				$sent = $this->sendMailRetargeting($case, $data["host"], $data["test"], $data["email_test"]);

				if ($sent === true) {

					// actualiza el contador de envíos en la tabla retargeting
					$dbData = [
						"atfinity_case_id" => $case["atfinity_case_id"],
						"onboarding_id" => $case["id"],
						"next_update" => $this->getTimeToNext($case["counter"]),
						"counter" => $case["counter"],
						"retargeting_id" => $case["retargeting_id"]
					];

					//actualiza las fechas en el registro de la tabla retargeting
					(new DBController())->updateDatesFields($dbData);

					// actualiza el contador el la tabla retargeting
					(new DBController())->updateCounterField($dbData);
				}
			}

			$this->logInfo("toSend...");
			$this->logInfo($toSend);

			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		/*
		 * Endpoint llamado desde el frontend para verificar la validez del los hash pasado en la url. 
		 */
		Router::post('/destination=checkHash', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$data = $req->getBody();

			if (array_key_exists("hash", $data)) {
				$this->response = (new DBController())->read($data);
			} else {
				$this->response['error'] === true;
			}

			// si el caso está en estados 3, 4 ó -1 el caso ya está cerrado y se devuelve false
			if ($this->response['error'] === false && $this->response['info']['state'] != -1 && $this->response['info']['state'] != 3 && $this->response['info']['state'] != 4) {
				$this->response = true;
			} else {
				$this->response = false;
			}

			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		// solicitud de documentación adicional a prospectos que cumplen requisitos
		Router::post('/destination=requestProspects', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$data = $req->getBody();

			$this->logInfo($data);

			// obtiene casos de la tabla onboarding estado (4) y camp prospects = 0
			$this->response['db']['cases'] = (new DBController())->getRequestProspectsFromOnboarding();

			$sendProspectEmail = [];

			foreach ($this->response['db']['cases']["info"] as $key => $case) {

				$atfinityCase = (new AtfinityController())->read([ "atfinity_case_id" => $case["atfinity_case_id"] ]);

				if ($atfinityCase["error"] === false) {

					$atfinityCase = json_decode($atfinityCase["info"]);

					$prospectCase = [];

					$prospectCase["onboarding_id"] = $case["id"];
					$prospectCase["atfinity_case_id"] = $case["atfinity_case_id"];
					$prospectCase["atfinity_instance_id"] = $case["atfinity_instance_id"];


					foreach ($atfinityCase->data->fields as $key2 => $value) {
						
						if ($value->key == "group_template") {
							if (is_object($value->value)) {
								$prospectCase["template"] = $value->value->key;
							} else {
								$prospectCase["template"] = "noaplica";
							}
						}

						if ($value->key == "first_name") {
							if (property_exists($value, 'value')) {
								$prospectCase["first_name"] = $value->value;
							} else {
								$prospectCase["first_name"] = "";
							}
						}

						if ($value->key == "last_name") {
							if (property_exists($value, 'value')) {
								$prospectCase["last_name"] = $value->value;
							} else {
								$prospectCase["last_name"] = "";
							}
						}

						if ($value->key == "email") {
							if (property_exists($value, 'value')) {
								$prospectCase["email"] = (filter_var($value->value, FILTER_VALIDATE_EMAIL)) ? $value->value : "";
							} else {
								$prospectCase["email"] = "";
							}
						}

						if ($value->key == "company") {
							if (property_exists($value, 'value')) {
								$prospectCase["company"] = $value->value;
							} else {
								$prospectCase["company"] = "";
							}
						}

						if ($value->key == "ng_tax_country1") {
							if (is_object($value->value)) {
								$prospectCase["ng_tax_country1"] = $value->value->key;
							} else {
								$prospectCase["ng_tax_country1"] = "";
							}
						}

						if ($value->key == "ng_worth") {
							if (is_object($value->value)) {
								$prospectCase["ng_worth"] = $value->value->label;
							} else {
								$prospectCase["ng_worth"] = "";
							}
						}

						if ($value->key == "web_language") {
							if (is_object($value->value)) {
								$prospectCase["language"] = $value->value->key;
							} else {
								$prospectCase["language"] = "";
							}
						}
					}

					if (!filter_var($prospectCase["email"], FILTER_VALIDATE_EMAIL)) {
						$prospectCase["template"] = "noaplica";
						$this->logError("Atfinity case: " . $prospectCase["atfinity_case_id"] . " invalid or empty email: " . $prospectCase["email"]);
					}

					if ($prospectCase["template"] != "noaplica") {
						$sendProspectEmail[] = $prospectCase;
					} else {
						// marca el campo prospects como enviado para no volver a tratarlo
						$prospectCase["send"] = true;
						(new DBController())->updateProspectField($prospectCase);
					}
				}
			}

			$fails = [];

			foreach ($sendProspectEmail as $key => $value) {
				$sendProspectEmail[$key]["ng_tax_country1"] = Utils::getCountryByCode($value["ng_tax_country1"], ($value["language"] == "en") ? "en" : "es");
				$sendProspectEmail[$key]["ng_worth"] = ($value["ng_worth"] == "More than 2M" && $value["language"] == "es") ? "Más de 2M" : $value["ng_worth"];
				$emailTemplate = EmailProspect::getTemplate($sendProspectEmail[$key], $data["host"]);
				$emailTo = $sendProspectEmail[$key]["email"];
				$subject = ($sendProspectEmail[$key]["language"] == "en") ? AppConfig::getInstance()->getValue("PROSPECT_SUBJECT_EN") : AppConfig::getInstance()->getValue("PROSPECT_SUBJECT_ES");
				$body = $emailTemplate;

				$this->logInfo("template: " . $sendProspectEmail[$key]["template"]);

			  $emailResult = $this->emailer->sendMail(
					$emailTo,
					$subject,
					$body,
					null,
					null,
					AppConfig::getInstance()->getValue("EMAIL_REPLY_RETARGETING_TO")
				);

				if ($emailResult["error"] === false) {
					$sendProspectEmail[$key]["send"] = true;

					// update ddbb
					$this->response['db']['update_prospect_state'] = (new DBController())->updateProspectField($sendProspectEmail[$key]);

					// update atfinity case
					$this->response['atfinity'] = (new AtfinityController())->updateProspectField(
						$sendProspectEmail[$key]["atfinity_case_id"],
						$sendProspectEmail[$key]["atfinity_instance_id"],
						true,
						""
					);

					$this->logInfo("email case send: " . $sendProspectEmail[$key]["atfinity_case_id"] . "- template: " . $sendProspectEmail[$key]["template"]);

				} else {

					$this->logInfo($emailResult["error"]);

					$fails[] = $sendProspectEmail[$key]["atfinity_case_id"];

					$this->response['atfinity'] = (new AtfinityController())->updateProspectField(
						$sendProspectEmail[$key]["atfinity_case_id"],
						$sendProspectEmail[$key]["atfinity_instance_id"],
						false,
						"email send error"
					);
				}
			}

			if (count($fails) > 0) {
				$this->logInfo("send emails fail cases id:");
				$this->logInfo($fails);
			}

			$this->logInfo("[end requestProspects process]");
			
			$res->status(200)->toJSON([
				"data" => $this->response
			]);

			exit;
		});

		// prueba odoo
		Router::post('/destination=odoo', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$data = $req->getBody();
			$this->logger->info("Onboarding service data:....");
			$this->logger->info($data);
			$response = (new OdooController())->send($data);
			$res->status(200)->toJSON([
				"response" => $response
			]);

			exit;
		});

	}

	private function boidas2Atfinity(array $data)
	{

		$response = false;

		if (
			is_array($data)
			&& count($data) > 0
			&& isset($data['validation_id'])
			&& isset($data['case_id'])
			&& filter_var($data['case_id'], FILTER_VALIDATE_INT)
			&& isset($data['instance_id'])
			&& filter_var($data['instance_id'], FILTER_VALIDATE_INT)
			&& filter_var($data['webhook_timestamp'], FILTER_VALIDATE_INT)
			&& isset($data['atfinity_instance_fields_id'])
			&& filter_var($data['atfinity_instance_fields_id'], FILTER_VALIDATE_INT)
			&& isset($data['state'])
		) {
			$validationId = filter_var($data['validation_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$caseId = filter_var($data['case_id'], FILTER_SANITIZE_NUMBER_INT);
			$instanceId = filter_var($data['instance_id'], FILTER_SANITIZE_NUMBER_INT);
			$webhook_timestamp = filter_var($data['webhook_timestamp'], FILTER_SANITIZE_NUMBER_INT);
			$instanceFieldsId = filter_var($data['atfinity_instance_fields_id'], FILTER_VALIDATE_INT);

			$actual_state = $data['state']; // estado actual del caso

			if ($instanceFieldsId) {

				// recoger datos de boidas
				$this->response['boidas']['validation'] = (new BoiDasController())->read(["validation_id" => $validationId, "sufix" => ""]);

				$this->logInfo('[boidas2atfinity:...]');
				$this->logInfo($this->response['boidas']);

				// enviar datos a atfinity
				if ($this->response['boidas']['validation']["error"] == false && $instanceFieldsId) {

					$dataBoidas = $this->response['boidas']['validation']['data'];

					$data2AtFinity = [];

					if (property_exists($dataBoidas, "id")) {
						if ($dataBoidas->id != "") {
							$data2AtFinity['boidas_id'] = $dataBoidas->id;
						}
					}

					if (property_exists($dataBoidas, "documentType")) {
						if ($dataBoidas->documentType != "") {
							$data2AtFinity['boidas_doc'] = $dataBoidas->documentType;
						}
					}

					if (property_exists($dataBoidas, "createdAt")) {
						if ($dataBoidas->createdAt != "") {
							$data2AtFinity['boidas_created'] = $dataBoidas->createdAt;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_Sex_Out")) {
						if ($dataBoidas->data->document->nodes->PD_Sex_Out->text != "") {
							$data2AtFinity['boidas_gender'] = $dataBoidas->data->document->nodes->PD_Sex_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_Name_Out")) {
						if ($dataBoidas->data->document->nodes->PD_Name_Out->text != "") {
							$data2AtFinity['boidas_name'] = $dataBoidas->data->document->nodes->PD_Name_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_LastName_Out")) {
						if ($dataBoidas->data->document->nodes->PD_LastName_Out->text != "") {
							$data2AtFinity['boidas_lastname'] = $dataBoidas->data->document->nodes->PD_LastName_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_BirthDate_Out")) {
						if ($dataBoidas->data->document->nodes->PD_BirthDate_Out->text != "") {
							$birthDate = explode(" ", trim($dataBoidas->data->document->nodes->PD_BirthDate_Out->text));
							$birthDateString = $birthDate[2] . "-" . $birthDate[1] . "-" . $birthDate[0];
							$data2AtFinity['boidas_birth'] = $birthDateString;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_Nationality_Out")) {
						if ($dataBoidas->data->document->nodes->PD_Nationality_Out->text != "") {
							$data2AtFinity['boidas_nationality'] = $dataBoidas->data->document->nodes->PD_Nationality_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "DD_DocumentNumber_Out")) {
						if ($dataBoidas->data->document->nodes->DD_DocumentNumber_Out->text != "") {
							$data2AtFinity['boidas_docnumber'] = $dataBoidas->data->document->nodes->DD_DocumentNumber_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "DD_ExpeditionDate_Out")) {
						$DD_ExpeditionDate_Out = $dataBoidas->data->document->nodes->DD_ExpeditionDate_Out->text;
						if ($DD_ExpeditionDate_Out != "" && preg_match("/^\s*\d+(?:\s+\d+)?(?:\s+\d+)?\s*$/", $DD_ExpeditionDate_Out)) {
							$docExpedition = preg_split("/\s+/", trim($DD_ExpeditionDate_Out));
							if (count($docExpedition) > 0) {
								switch (count($docExpedition)) {
									case 1:
										$docExpeditionString = sprintf("%d-01-01", $docExpedition[0]);
										break;
									case 2:
										$docExpeditionString = sprintf("%d-%02d-01", $docExpedition[1], $docExpedition[0]);
										break;
									default:
										$docExpeditionString = sprintf("%d-%02d-%02d", $docExpedition[2], $docExpedition[1], $docExpedition[0]);
								}
								$data2AtFinity['boidas_docexpedition'] = $docExpeditionString;
							}
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "DD_ExpirationDate_Out")) {
						$DD_ExpirationDate_Out = $dataBoidas->data->document->nodes->DD_ExpirationDate_Out->text;
						if ($DD_ExpirationDate_Out != "" && preg_match("/^\s*\d+(?:\s+\d+)?(?:\s+\d+)?\s*$/", $DD_ExpirationDate_Out)) {
							$docExpiration = preg_split("/\s+/", trim($DD_ExpirationDate_Out));
							if (count($docExpiration) > 0) {
								switch (count($docExpiration)) {
									case 1:
										$docExpirationString = sprintf("%d-01-01", $docExpiration[0]);
										break;
									case 2:
										$docExpirationString = sprintf("%d-%02d-01", $docExpiration[1], $docExpiration[0]);
										break;
									default:
										$docExpirationString = sprintf("%d-%02d-%02d", $docExpiration[2], $docExpiration[1], $docExpiration[0]);
								}
								$data2AtFinity['boidas_docexpiration'] = $docExpirationString;
							}
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "DD_IssuingCountry_Out")) {
						if ($dataBoidas->data->document->nodes->DD_IssuingCountry_Out->text != "") {
							$data2AtFinity['boidas_doccountry'] = $dataBoidas->data->document->nodes->DD_IssuingCountry_Out->text;
						}
					}

					if (property_exists($dataBoidas->data->document->nodes, "PD_IdentificationNumber_Out")) {
						if ($dataBoidas->data->document->nodes->PD_IdentificationNumber_Out->text != "") {
							$data2AtFinity['boidas_docidentnum'] = $dataBoidas->data->document->nodes->PD_IdentificationNumber_Out->text;
						}
					}

					if (property_exists($dataBoidas->data, "summary")) {
						$data2AtFinity['boidas_scoretotal'] = round($this->getValueFromArray($dataBoidas->data->summary->scores, "ValidationGlobalScore"), 3);
					}

					if (property_exists($dataBoidas->data, "biometry")) {
						/*
						$data2AtFinity['boidas_scoreselfie'] = round($dataBoidas->data->biometry->scores[0]->value, 3);
						$data2AtFinity['boidas_scorephoto'] = round($dataBoidas->data->biometry->scores[1]->value, 3);
						$data2AtFinity['boidas_scorelife'] = round($dataBoidas->data->biometry->scores[2]->value, 3);
						$data2AtFinity['boidas_scoredoc'] = round($dataBoidas->data->document->scores[2]->value, 3);
						*/

						$data2AtFinity['boidas_scoreselfie'] = round($this->getValueFromArray($dataBoidas->data->biometry->scores, "ValidasScoreSelfie"), 3);
						$data2AtFinity['boidas_scorephoto'] = round($this->getValueFromArray($dataBoidas->data->biometry->scores, "ValidasScorePhotoId"), 3);
						$data2AtFinity['boidas_scorelife'] = round($this->getValueFromArray($dataBoidas->data->biometry->scores, "ValidasScoreLifeProof"), 3);
					}
					if (property_exists($dataBoidas->data, "document")) {
						$data2AtFinity['boidas_scoredoc'] = round($this->getValueFromArray($dataBoidas->data->document->scores, "Score-DocumentGlobal"), 3);
						$data2AtFinity['boidas_scoredoc1'] = round($this->getValueFromArray($dataBoidas->data->document->scores, "ScoreGroup-PhotoAuthenticity"), 3);
						$data2AtFinity['boidas_scoredoc2'] = round($this->getValueFromArray($dataBoidas->data->document->scores, "ScoreGroup-PrintAttackTest"), 3);
						$data2AtFinity['boidas_scoredoc3'] = round($this->getValueFromArray($dataBoidas->data->document->scores, "ScoreGroup-ReplayAttackTest"), 3);
					}



					$fields = [];
					$fields['fields'] = [];
					foreach ($data2AtFinity as $k => $v) {
						$fields['fields'][] = [
							"instance_id" => $instanceFieldsId,
							"information_key" => $k,
							"value" => $v
						];
					}

					// TODO actualizar la información en atfinity
					$this->response['atfinity'] = (new AtfinityController())->update($fields, $caseId, false);

					// recoger los proofs ids de los ficheros a subir
					$responseProof = (new AtfinityController())->getProofIds($caseId);
					$dataProofIds = [];
					if ($responseProof && !$responseProof['error']) {
						$dataProofIds = $responseProof['data'];
					}

					// imágenes
					$pic1 = base64_encode((new BoiDasController())->getPicture($dataBoidas->data->document->_links[0]->href, $this->response['boidas']['validation']['token']));
					$pic2 = base64_encode((new BoiDasController())->getPicture($dataBoidas->data->document->_links[1]->href, $this->response['boidas']['validation']['token']));
					$pic3 = base64_encode((new BoiDasController())->getPicture($dataBoidas->data->document->_links[2]->href, $this->response['boidas']['validation']['token']));
					$appConfig = AppConfig::getInstance();
					$files = array(
						'boidas_pic1' => array(
							"file" => $pic1,
							"id" => (isset($dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC1")])) ? $dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC1")] : 0
						),
						'boidas_pic2' => array(
							"file" => $pic2,
							"id" => (isset($dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC2")])) ? $dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC2")] : 0
						),
						'boidas_pic3' => array(
							"file" => $pic3,
							"id" => (isset($dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC3")])) ? $dataProofIds[$appConfig->getValue("PROOF_ID_NG_BOIDAS_PIC3")] : 0
						)
					);

					foreach ($files as $name => $file) {
						if ($file['id'] > 0) {
							$this->response['atfinity_' . $name] = (new AtfinityController())->uploadFileWithId($caseId, $file['id'], $name, $file['file']);
						} else {
							$this->response['atfinity_' . $name] = (new AtfinityController())->uploadFile($caseId, $name, $file['file']);
						}
					}

					// si el estado es 3, pasa el estado a 4, si no mantiene los estados -1 ó -3
					if ($actual_state == Constants::STATE_ESIGN_OK) {
						$case_state = Constants::STATE_BOIDAS2ATFINITY_OK; // All ok happy path,
						$boidas_status = "boidas_status4";
					} else {
						$case_state = $actual_state;
						$boidas_status = ($case_state == Constants::STATE_ERROR_IFRAME_VALIDAS2BOIDAS_OK) ? "boidas_statusminus1" : "boidas_statusminus3";
					}

					// update atfinity state
					$fields['fields'][] = [
						"instance_id" => $instanceFieldsId,
						"information_key" => 'boidas_status',
						"value" => $boidas_status // para atfinity
					];
					$this->response['atfinity_update_boidas_state'] = (new AtfinityController())->update($fields, $caseId, false);

					// update state in db
					$date = new \DateTime();
					$date = $date->format("Y-m-d H:i:s");
					$dbData = [
						"case_id" => $caseId,
						"instance_id" => $instanceId,
						"last_update" => $date,
						"webhook_timestamp" => $webhook_timestamp,
						"state" => $case_state,
						"boidas_to_atfinity" => 1
					];
					$this->response['db']['update_state'] = (new DBController())->updateStateBoidasToAtfinity($dbData);

					// si es estado 4 se realiza la transición del caso en Atfinity

					if ($case_state == Constants::STATE_BOIDAS2ATFINITY_OK) {

						$transitionId = AppConfig::getInstance()->getValue("TRANSITION_NG_REVIEW");

						$this->response['transition'] = (new AtfinityController())->stateTransition($caseId, $transitionId);

						if ($this->response['transition']['error'] === false) {
							$resp = json_decode($this->response['transition']["info"]);
							if (is_object($resp)) {
								if (property_exists($resp, "data")) {
									if (property_exists($resp->data, "id")) {
										$this->logInfo("case " . $resp->data->id . " -> transition: " . $transitionId . ": ok");
									}
								}
							} else {
								$this->logInfo("case " . $caseId . " -> transition: " . $transitionId . ": fail");
								$this->logInfo($resp);
							}
						}
					}

					$response = true;
				}
			}
		}

		return $response;
	}

	private function getTimeToNext($counter)
	{

		$timeToNext = "";

		switch ($counter) {
			case 0:
				$timeToNext = "P7D"; // 7 días
				break;

			case 1:
				$timeToNext = "P7D"; // 14 días
				break;

			case 2:
				$timeToNext = "P7D"; // 21 días
				break;

			case 3:
				$timeToNext = "P7D"; // 28 días
				break;

			case 4:
				$timeToNext = "P7D"; // 35 días
				break;

			default:
				// code...
				break;
		}

		return $timeToNext;
	}

	private function getValueFromArray($arr, $name)
	{
		foreach ($arr as $key => $obj) {
			if ($obj->name === $name) {
				return $obj->value;
			}
		}
		return 0;
	}

	private function sendMailRetargeting($case, $host, $test, $email_test)
	{

		switch ($case['counter']) {
			case 0:
				$subject = ($case["language"] == "en") ? "👍 " . $case["first_name"] . ", here's some good news for you" : "👍 " . $case["first_name"] . ", tenemos buenas noticias para ti";
				$body = EmailMessages::emailCase0($case["first_name"], $case["language"], $host, $case["hash"]);
				break;

			case 1:
				$subject = ($case["language"] == "en") ? "✔ Do well for your future, may I help you? " : "✔ Hazlo bien por tu futuro, ¿te ayudo?";
				$body = EmailMessages::emailCase1($case["first_name"], $case["language"], $host, $case["hash"]);
				break;

			case 2:
				$subject = ($case["language"] == "en") ? "🔋 Are you 100%? Activate New Gen " : "🔋  ¿Estás al 100%? Activa New Gen ";
				$body = EmailMessages::emailCase2($case["first_name"], $case["language"], $host, $case["hash"]);
				break;

			case 3:
				$subject = ($case["language"] == "en") ? "🇨🇭 Your account in Switzerland, activate New Gen" : "🇨🇭 Tu cuenta en Suiza, activa New Gen";
				$body = EmailMessages::emailCase3($case["first_name"], $case["language"], $host, $case["hash"]);
				break;

			case 4:
				$subject = ($case["language"] == "en") ? "🚨 3,2,1... Action! " : "🚨 3,2,1... Acción!";
				$body = EmailMessages::emailCase4($case["first_name"], $case["language"], $host, $case["hash"]);
				break;

			default:
				$subject = "";
				$body = "";
				break;
		}

		$emailTo = ($email_test == "") ? $case["email"] : $email_test;

		$emailResult = $this->emailer->sendMailRetargeting(
			$emailTo,
			$subject,
			$body,
			null,
			$case["first_name"],
			AppConfig::getInstance()->getValue("EMAIL_REPLY_RETARGETING_TO")
		);

		$this->logInfo("email send to..." . $emailTo);

		return $emailResult;
	}

	/*
	 * comprueba si la fecha actual es mayor que la fecha pasada en el parámetro
	 */
	private function checkIfSend(string $updateDate)
	{
		$date = new \DateTime();
		$nextDate = new \DateTime($updateDate);
		$difference = $date->diff($nextDate);
		return ($difference->invert === 1) ? true : false;
	}

	private function getDocumentType($data)
	{
		$document_type = "XX_Passport_YYYY";

		if ($data['identity_document'] === "id") {
			$countryCode = 'es';

			// paso 3 (retargeting), primer país
			if (isset($data['country_identity_document']) && intval($data['country_identity_document']) === 1 && isset($data['nationality_retargeting']) && $data['nationality_retargeting'] !== '') {
				$countryCode = $data['nationality_retargeting'];

				// paso 3 (retargeting), segundo país
			} else if (isset($data['country_identity_document']) && intval($data['country_identity_document']) === 2 && isset($data['nationality_one_retargeting']) && $data['nationality_one_retargeting'] !== '') {
				$countryCode = $data['nationality_one_retargeting'];

				// paso 3 (retargeting), sólo un país
			} else if (isset($data['country_identity_document_one']) && intval($data['country_identity_document_one']) === 1 && isset($data['nationality_retargeting']) && $data['nationality_retargeting'] !== '') {
				$countryCode = $data['nationality_retargeting'];

				// paso 1, primer país
			} else if (isset($data['country_identity_document']) && intval($data['country_identity_document']) === 1 && isset($data['nationality']) && $data['nationality'] !== '') {
				$countryCode = $data['nationality'];

				// paso 1, segundo país
			} else if (isset($data['country_identity_document']) && intval($data['country_identity_document']) === 2 && isset($data['nationality_one']) && $data['nationality_one'] !== '') {
				$countryCode = $data['nationality_one'];
			}

			foreach ($this->countries as $country) {

				if ($country['code'] === $countryCode && isset($country['document_type'])) {
					$document_type = $country['document_type'];
					break;
				}
			}
		}

		return $document_type;
	}

	private function getAtfinityCaseById(int $id)
	{

		$this->logInfo('[getAtfinityCaseById... ]');
		$this->logInfo('[case_id: ' . $id);

		$response["atfinity"] = "";
		$response["atfinity_case_first_name"] = "";
		$response["atfinity_case_last_name"] = "";
		$response["atfinity_case_email"] = "";
		$response["error"] = true;

		if ($id > 0) {

			$response["atfinity"] = (new AtfinityController())->read(["atfinity_case_id" => $id]);

			if ($response["atfinity"]["error"] === false) {

				$response["error"] = false;

				$fields = json_decode($response["atfinity"]["info"]);
				foreach ($fields->data->fields as $key => $value) {
					if ($value->key == "first_name") {
						$response["atfinity_case_first_name"] = $value->value;
					}
					if ($value->key == "last_name") {
						$response["atfinity_case_last_name"] = $value->value;
					}
					if ($value->key == "email") {
						$response["atfinity_case_email"] = $value->value;
					}
				}
			}
		}

		return $response;
	}
}
