<?php

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Email\Emailer;
use METRIC\App\Service\Utils;
use METRIC\App\Controller\BaseController;



class BoiDasController extends BaseController implements ControllerInterface
{

  private $emailer;
  private $settings = [];
  private $response = [];

  private $tokenFilePath = "";

  public function __construct()
  {
    parent::__construct();
    $this->tokenFilePath = sprintf("%s/boidas_token.txt", realpath(__DIR__ . '/../../../var'));
    $this->settings = [
      "client_id" => AppConfig::getInstance()->getValue("BOIDAS_CLIENT_ID"),
      "client_secret" => AppConfig::getInstance()->getValue("BOIDAS_CLIENT_SECRET"),
      "username" => AppConfig::getInstance()->getValue("BOIDAS_USERNAME"),
      "password" => AppConfig::getInstance()->getValue("BOIDAS_PASSWORD"),
      "endpoint" => AppConfig::getInstance()->getValue("BOIDAS_ENDPOINT"),
      "certificate" => AppConfig::getInstance()->getValue("BOIDAS_CERTTIFICATE"),
    ];

    $this->emailer = new Emailer();
  }

  public function read(array $data)
  {
    $this->logInfo(json_encode($data));
    $token = $this->getToken($this->tokenFilePath);
    $this->response = $this->getValidationData($token, $data["validation_id"], $data["sufix"]);
    $this->response = $this->checkBoidasReadResponse($this->response, $token);
    return $this->response;
  }

  public function send(array $data)
  {
  }

  public function update(array $data)
  {
  }


  /*
   * Checkea que el servicio esté disponible
   */
  public function isAlive()
  {
    $curl = curl_init();
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => sprintf("%s/api/v1/alive", $this->settings['endpoint']),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => self::TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
      )
    );

    $response = curl_exec($curl);
    $http = 200;

    if (curl_errno($curl)) {
      $this->logError("ko");
      $this->logError(curl_error($curl));
      $this->logError($response);
      $this->response['error'] = true;
      $this->response['info'] = curl_error($curl);
    } else {
      $http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      $this->logInfo("ok");
      $this->logInfo(sprintf("http: %s", $http));
    }

    curl_close($curl);

    return ($http === 204);
  }

  public function getToken(string $path)
  {
    $token = $this->getTokenFromRemote();
    $this->logInfo(sprintf("[boidas token: %s]", $token));
    return $token;
  }

  /*
   * Comprueba si la repuesta del servicio Atfinity ha sido
   * exitosa y contiene un case_id.
   * Retorna un array con información sobre el caso y si hay error o no
   */
  public function checkBoidasReadResponse($curlResponse, $token)
  {

    $response = [];
    
    if ($curlResponse !== null) {
      $resp = json_decode($curlResponse);
      $response['error'] = (isset($resp->data->id)) ? false : true;
      $response['token'] = $token;
      $response['info'] = ($response['error']) ? $resp : ""; // se registra la respuesta si hay error
      $response['data'] = ($response['error']) ? "" : $resp->data;
      $response['validation_id'] = (isset($resp->data->id)) ? $resp->data->id : "";
    } else {
      $this->logError("curlResponse param is null");
      $response['error'] = true;
      $response['token'] = $token;
      $response['info'] = "";
      $response['data'] = "";
      $response['validation_id'] = "";
    } 

    return $response;
  }

  /*
   * Petición curl API para obtener información de la validación de un caso
   */
  public function getValidationData(string $token, string $validationId, string $sufix = null)
  {

    $response = null;

    $this->logInfo($token);
    $this->logInfo($validationId);
    $this->logInfo(($sufix) ? sprintf("%s/api/v1/validation/%s/%s", $this->settings['endpoint'], $validationId, $sufix)
      : sprintf("%s/api/v1/validation/%s", $this->settings['endpoint'], $validationId));

    $options = [
      CURLOPT_URL => ($sufix) ? sprintf("%s/api/v1/validation/%s/%s", $this->settings['endpoint'], $validationId, $sufix)
        : sprintf("%s/api/v1/validation/%s", $this->settings['endpoint'], $validationId),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'Authorization: Bearer ' . $token
      )
    ];

    // utilizar certificado en el contexto de bbva
    if (AppConfig::getInstance()->getValue("CONTEXT") == "bbva") {
      $options[CURLOPT_CAINFO] = $this->settings["certificate"];
    }

    if (!empty($token) && !empty($validationId)) {
      $curl = curl_init();

      curl_setopt_array($curl, $options);

      $response = curl_exec($curl);

      if (curl_errno($curl)) {
        $this->logError(curl_error($curl));
      } else {
        $resp = json_decode($response);
        $this->logInfo(sprintf("[validation_id: %s]", $resp->data->id));
      }

      curl_close($curl);
    }

    return $response;
  }


  public function getTokenFromRemote()
  {

    $access_token = null;

    $options = [
      CURLOPT_URL => sprintf("%s/api/v1/oauth/token/", $this->settings['endpoint']),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => array(
        'grant_type' => 'password',
        'username' => $this->settings["username"],
        'password' => $this->settings["password"]
      ),
      CURLOPT_USERPWD => sprintf("%s:%s", $this->settings['client_id'], $this->settings['client_secret']),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'Content-Type: multipart/form-data'
      )
    ];

    // utilizar certificado en el contexto de bbva
    if (AppConfig::getInstance()->getValue("CONTEXT") == "bbva") {
      $options[CURLOPT_CAINFO] = $this->settings["certificate"];
    }

    $curl = curl_init();
    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $resp = json_decode($response);

    $access_token = false;

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError($response);
    } else {
      if ((isset($resp->access_token) && isset($resp->expires_in))) {
        $this->logInfo("[access_token: " . $resp->access_token . "]");
        $access_token = $resp->access_token;
      } else {
        $this->logError("[error al obtener el token: " . $response . "]");
      }
    }

    curl_close($curl);

    return $access_token;
  }

  public function getPicture($url, $token)
  {

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token
      ),
    ];

    // utilizar certificado en el contexto de bbva
    if (AppConfig::getInstance()->getValue("CONTEXT") == "bbva") {
      $options[CURLOPT_CAINFO] = $this->settings["certificate"];
    }

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError($response);
    }

    curl_close($curl);

    return $response;
  }
}
