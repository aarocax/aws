<?php 

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Email\Emailer;
use METRIC\App\Service\Crypto;
use METRIC\App\Controller\BaseController;
use METRIC\App\Logger\Logger;
use METRIC\App\Controller\DBController;

class EsignController extends BaseController implements ControllerInterface
{

  private $emailer;
  private $settings = [];
  private $response = [];
  private $tokenFilePath = "";
  private $logger;
  
  public function __construct()
  {
    parent::__construct();
    $this->settings = [
        "api_key" => AppConfig::getInstance()->getValue("ESIGN_API_KEY"),
        "api_endpoint" => AppConfig::getInstance()->getValue("ESIGN_ENDPOINT"),
        "webhook_token" => AppConfig::getInstance()->getValue("ESIGN_WEBHOOK_TOKEN")
    ];
    
    $this->emailer = new Emailer();
    $this->logger = new Logger;
  }

  public function read(array $data)
  {
  }


  public function prepareData(array $data) {

    $date = new \DateTime();
    $date = $date->format('YmdHis');

    $fileBin = base64_decode($data["file"]);

    $temp = tmpfile();
    $meta_data = stream_get_meta_data($temp);
    $tempName = $meta_data["uri"];
    file_put_contents($tempName, $fileBin);


    $response = array(
      'file'=> new \CURLFILE($tempName, "application/pdf", $data["name"] . "_" . $date),
      'name' => $data["name"] . "_" . $date,
      'form_fields_per_document' => '[
        {
          "name": "",
          "x": 165,
          "y": 240,
          "page": 2,
          "type": "signature",
          "signer_index": 0,
          "value": ""
        }
      ]'
    );
    return $response;

  }

  public function send(array $data)
  {

    $this->logInfo("[data: ]");

    $dataPrepared = $this->prepareData($data);
    $response = $this->sendData($data);
    $this->response = $this->checkEsignSendResponse($response);
    
    return $this->response;
  }



  public function update(array $data)
  {
  }


  public function sendData(array $data)
  {

    $curl = curl_init();

    $date = new \DateTime();
    $date = $date->format('YmdHis');

    $temp = tmpfile();
    $meta_data = stream_get_meta_data($temp);
    $tempName = $meta_data["uri"];
    file_put_contents($tempName, $data["file"]);
    

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . '/library_documents',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
      'file'=> new \CURLFILE($tempName, "application/pdf", $data["name"] . "_" . $date . ".pdf"),
      'name' => $data["name"] . "_" . $date,
      'form_fields_per_document' => '[
        {
          "name": "",
          "x": 155,
          "y": 255,
          "page": 18,
          "type": "signature",
          "signer_index": 0,
          "value": ""
        }
      ]'
    ),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: multipart/form-data',
        'Accept: application/json',
        'apikey: ' . $this->settings["api_key"]
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError("send curl response[data: " . json_encode($response));
      $this->logError(curl_error($curl));
      $this->logError("[response sendData:] [data: " . $response . "]");
    } else {
      $this->logInfo("send curl response[data: " . json_encode($response));
    }

    curl_close($curl);

    return $response;
  }

  public function webHook($payload)
  {
    $this->response["error"] = false;

    try {

      // save event to file
      $date = new \DateTime();
      $fileName = 'esign_event_'.$date->format('Ymd_H:i:s:u').'.json';
      $file = __DIR__ . '/../../../var/esign_events/' . $fileName;
      file_put_contents($file, $payload[0]);
      
      $this->logInfo("event saved: " . $fileName);

      $payload[0] = ltrim($payload[0], '"');
      $payload[0] = rtrim($payload[0], '"');

      $this->logInfo($payload[0]);
      

      $event = json_decode($payload[0]);

      if ($event->event_type == "signature_signed" || $event->event_type == "signature_request_timestamped" || $event->event_type == "signature_declined") {

        // se checkea que el origen del evento es veridas
        $veridasOrigin = Crypto::checkOrigin($event->event_time, $event->event_type, $this->settings["webhook_token"], $event->event_hash);

        $this->logInfo("checked veridas origin: " . $veridasOrigin);

        if ($veridasOrigin) {
          $this->logInfo("event signature_request_id: " . $event->signature_request_id);

          $this->response["signature_request_id"] = $event->signature_request_id;
          $this->response["event_time"] = $event->event_time;
          $this->response["event_type"] = $event->event_type;

          // actualizar bbdd para enlazar validation_id y signature_request_id
          if ($event->event_type == "signature_signed" || $event->event_type == "signature_declined") {

            $this->logInfo("event validation_id: " . $event->event_metadata->validation_id);

            $this->response["validation_id"] = $event->event_metadata->validation_id;

            $dbData = [
              "validation_id" => $event->event_metadata->validation_id,
              "signature_request_id" => $event->signature_request_id
            ];
  
            //actualiza signature_request_id en el registro de la tabla onboarding
            (new DBController())->updateSignatureRequestId($dbData);

          // descargar el zip
          } else if ($event->event_type == "signature_request_timestamped") {

              // get signed document from esign
              $curl = curl_init();

              curl_setopt_array($curl, array(
                CURLOPT_URL => $this->settings["api_endpoint"] . '/signature_request/' . $event->signature_request_id . '/files?folder=all',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => self::TIMEOUT,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                  'Accept: application/json',
                  'apikey: ' . $this->settings["api_key"]
                ),
              ));

              $curlInfo = curl_getinfo($curl);
              $this->logInfo(json_encode($curlInfo));
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              $this->logInfo($httpcode);

              $response = curl_exec($curl);

              if (curl_errno($curl)){
                $this->logError(curl_error($curl));
                $this->logError("[response:] [data: " . json_encode($response) . "]");
                $this->response["error"] = true;
              } else {
                $this->logInfo("[response data: downloaded signature documents zip file]");
                $this->response["file"] = $response;
              }
              curl_close($curl);
          }
        }
      }
    } catch (\Exception $e) {
      $this->logError($e->getMessage());
      $this->response["error"] = true;
    }



    return $this->response;
  }

  public function setWebHookUrl(string $webHookUrl)
  {

    $this->response["error"] = false;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . '/settings',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS =>'{
        "default_locale": "EN",
        "webhook_url": "' . $webHookUrl . '",
        "webhook_token": "' . $this->settings["webhook_token"] . '"
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'apikey: ' . $this->settings["api_key"]
      ),
    ));

    $response = curl_exec($curl);

    $this->response["info"] = $response;

    if (curl_errno($curl)) {
      $this->logError("send curl response[data: " . json_encode($response));
      $this->logError(curl_error($curl));
      $this->logError("[response sendData:] [data: " . $response . "]");
      $this->response["error"] = true;
      $this->response["info"] = true;
    } else {
      $this->logInfo("send curl response[data: " . json_encode($response));
    }

    curl_close($curl);

    return $this->response;
  }

  public function checkEsignSendResponse(string $curlResponse) {
    $resp = json_decode($curlResponse);
    $response = [];

    $response['error'] = ($this->existId($resp)) ? false : true ;
    $response['info'] = ($response['error']) ? $resp : "";
    $response['library_document_id'] =  ($response['error']) ? "" : $resp->id;

    return $response;
  }


  public function existId($response)
  {
    $resp = false;
    if (is_object($response)) {
      $resp = (property_exists($response, "id")) ? true : false;
    }
    return $resp;
  }

  public function getSignatureRequest()
  {

    $this->response["error"] = false;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . '/signature_request',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'apikey: ' . $this->settings["api_key"]
      ),
    ));

    $response = curl_exec($curl);

    $this->response["info"] = $response;

    if (curl_errno($curl)) {
      $this->logError("send curl signature_request response[data: " . json_encode($response));
      $this->logError(curl_error($curl));
      $this->logError("[response sendData:] [data: " . $response . "]");
      $this->response["error"] = true;
      $this->response["info"] = true;
    } else {
      $this->logInfo("send curl signature_request response[data: " . json_encode($response));
    }

    curl_close($curl);

    return $this->response;
  }

  public function signatureRequestFinish(string $signature_request_id)
  {

    $this->response["error"] = false;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . '/signature_request/' . $signature_request_id . '/finish',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'apikey: ' . $this->settings["api_key"]
      ),
    ));

    $response = curl_exec($curl);

    $this->response["info"] = $response;

    if (curl_errno($curl)) {
      $this->logError("send curl signature_request_finish response[data: " . json_encode($response));
      $this->logError(curl_error($curl));
      $this->logError("[response sendData:] [data: " . $response . "]");
      $this->response["error"] = true;
      $this->response["info"] = true;
    } else {
      $this->logInfo("send curl signature_request_finish response[data: " . json_encode($response));
    }

    curl_close($curl);

    return $this->response;
  }


}
