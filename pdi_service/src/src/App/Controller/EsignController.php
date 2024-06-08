<?php

namespace METRIC\App\Controller;

use METRIC\App\Controller\BaseController;
use METRIC\App\HttpClient\EsignHttpClient;

use METRIC\App\Config\AppConfig;

/**
 * EsignController
 */
class EsignController extends BaseController
{
    private EsignHttpClient $esignHttpClient;
    private $response = [];

    /**
     * __construct
     *
     * @param  mixed $esignHttpClient
     * @return void
     */
    public function __construct(EsignHttpClient $esignHttpClient)
    {
        parent::__construct();
        $this->esignHttpClient = $esignHttpClient;
    }

    public function sendDocument(array $data): array
    {
        $esignResponse = $this->esignHttpClient->sendDocument($data);
        $this->logInfo("sendDocument esignResponse.....");
        $this->logInfo($esignResponse);
        return $this->checkEsignSendResponse($esignResponse);
    }

    private function checkEsignSendResponse($esignResponse): array
    {
        $resp = json_decode($esignResponse);
        $response = [];

        $this->logInfo("resp");
        $this->logInfo($resp);

        $response['error'] = ($this->existId($resp)) ? false : true;
        $response['info'] = ($response['error']) ? $resp : "";
        $response['library_document_id'] =  ($response['error']) ? "" : $resp->id;

        return $response;
    }

    private function existId($response): bool
    {
        $resp = false;
        if (is_object($response)) {
            $resp = (property_exists($response, "id")) ? true : false;
        }
        return $resp;
    }
}
