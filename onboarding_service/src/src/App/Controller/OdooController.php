<?php

namespace METRIC\App\Controller;

use Ripcord\Ripcord;
use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Exception\OdooException;
use METRIC\App\Controller\BaseController;
use METRIC\App\Service\Utils;

class OdooController extends BaseController implements ControllerInterface
{

  private $settings = [];
  private $response = [];

  public function __construct()
  {
    parent::__construct();
  }

  public function read(array $data)
  {
    $this->response['error'] = false;
    $this->response['info'] = "";
    return $this->response;
  }

  public function send(array $data)
  {
    $this->response["contact_id"] = $this->sendData($data);
    $this->response['error'] = !is_numeric($this->response["contact_id"]);
    $this->response['info'] = "";
    return $this->response;
  }

  public function redirectUrl(array $data)
  {
    $this->logInfo($data[0]);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $data[0],
      CURLOPT_RETURNTRANSFER => true,
      //CURLOPT_ENCODING => '',
      //CURLOPT_MAXREDIRS => 10,
      //CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_TIMEOUT => 2,
      //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    $responseInfo = curl_getinfo($curl);

    if ($responseInfo["url"] == "") {
      $this->logError(curl_error($curl));
      $this->logError("[response read:] [data: " . $response . "]");
      $this->response['error'] = true;
    } else {
      $this->logInfo("[odoo links response: ]");
      $this->logInfo($response);
      $this->logInfo("odoo links response info: ");
      $this->logInfo($responseInfo);
      $this->response['error'] = false;
    }

    curl_close($curl);

    $this->response['info'] = $responseInfo;

    return $this->response;
  }

  public function update(array $data)
  {
    $this->response['error'] = false;
    $this->response['info'] = "";
    return $this->response;
  }

  private function sendData(array $preparedData)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => AppConfig::getInstance()->getValue("ODOO_SERVICE") . '/destination=odoo',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query($preparedData),
      CURLOPT_HTTPHEADER => array(
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.9,es;q=0.8,pt;q=0.7,es-CO;q=0.6,es-AR;q=0.5,gl;q=0.4',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response:] [data: " . json_encode($response) . "]");
      $this->response['error'] = true;
      $this->response['info'] = curl_error($curl);
    } else {
      $this->response['error'] = false;
      $this->response['file'] = $response;
      $this->logInfo("ok");
    }

    curl_close($curl);

    return $this->response;
  }

}
