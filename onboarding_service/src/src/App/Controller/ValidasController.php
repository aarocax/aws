<?php

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Controller\BaseController;
use METRIC\App\Config\AppConfig;
use METRIC\App\Email\Emailer;
use METRIC\App\Service\Utils;

class ValidasController extends BaseController {

    private $emailer;
    private $settings = [];
    private $response = [];
    
    public function __construct()
    {

        parent::__construct();

        $this->settings = [
            "api_key" => AppConfig::getInstance()->getValue("VALIDAS_API_KEY"),
            "api_endpoint" => AppConfig::getInstance()->getValue("VALIDAS_ENDPOINT")
        ];
        
        $this->emailer = new Emailer();
    }


    public function confirm(array $data)
    {
        $this->logInfo(json_encode($data));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => sprintf("%s/validation/%s/confirmation", $this->settings["api_endpoint"], $data["validation_id"]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => [],
            CURLOPT_HTTPHEADER => array(
                'apikey: ' . $this->settings["api_key"],
                'content-type: multipart/form-data',
                'cache-control: no-cache'
            )
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->logInfo("[httpcode: ]" . $httpcode);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl));
            $this->logError("[response sendData:] [data: " . $response . "]");
        } else {
            $this->logInfo("[response : " . $response . "]");
            $this->logInfo(sprintf("%s/validation/%s/confirmation", $this->settings["api_endpoint"], $data["validation_id"]));
        }

        $resp = json_decode($response);
        if (!is_array($resp)) {
            $resp = [];
        }
        $resp['httpcode'] = $httpcode;

        curl_close($curl);

        return $resp;
    }




    public function getResults(array $data)
    {
        $this->logInfo(json_encode($data));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => sprintf("%s/validation/%s", $this->settings["api_endpoint"], $data["validation_id"]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'apikey: ' . $this->settings["api_key"],
                'content-type: multipart/form-data',
                'cache-control: no-cache'
            )
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl));
            $this->logError("[response read:] [data: " . $response . "]");
            $this->response['error'] = true;
        } else {
            $resp = json_decode($response);
            // $this->logInfo("[response getResults: ]");
            // $this->logInfo( $resp->data->data->summary);
            $this->response['error'] = false;
            //$this->response['info']['ValidationGlobalScore'] = $resp->data->data->summary->scores[0]->value;
        }

        curl_close($curl);
        
        $this->response['info'] = json_decode($response);

        return $this->response;
    }



}