<?php

namespace METRIC\App\HttpClient;

use METRIC\App\Controller\BaseController;
use stdClass;
use METRIC\App\Config\AppConfig;

class AtfinityHttpClient extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * getCases
     * Recupera la lista de casos cuyo estadao es "Waitin for url"
     *
     * @param  mixed $page
     * @param  mixed $pageSize
     * @return array
     */
    public function getCases(int $page = 1, int $pageSize = 50): array | null
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT") . '?page=' . $page . '&page_size=' . $pageSize . '&search=url',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
                'Accept: application/json',
                'Accept-Language: en'
            ),
        ));

        $response = curl_exec($curl);

        return json_decode($response, true);
    }

    /**
     * getCase
     * Recupera un caso por su id de caso
     * 
     * @param  int $caseId
     * @return null null|bool|string
     */
    public function getCase(int $caseId): null|bool|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT") . '/' . $caseId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
                'Accept: application/json',
                'Accept-Language: en'
            ),
        ));

        $response = [];

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl), true);
            $this->logError($response, true);
        }

        curl_close($curl);

        return $response;
    }

    /**
     * updateCase
     *
     * @param  mixed $caseId
     * @param  mixed $fieldInstanceId
     * @param  mixed $fieldInformationKey
     * @param  mixed $fieldValue
     * @return null|bool|string
     */
    public function updateCase(int $caseId, array $data): null|bool|string
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT") . '/' . $caseId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
                'Accept: application/json',
                'Accept-Language: en',
                'If-Match: bogus',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl), true);
            $this->logError($response, true);
        }

        $this->logInfo("response curl...");
        $this->logInfo($response);

        curl_close($curl);
        return $response;
    }

    /**
     * transition
     *
     * @param  mixed $caseId
     * @param  mixed $transitionId
     * @return void
     */
    public function transition(int $caseId, int $transitionId): null|bool|string
    {

        $data = [
            "transition_id" => $transitionId
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT") . '/' . $caseId . '/transition',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
                'Accept: application/json',
                'Content-Type: application/json',
                'Accept-Language: en'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function getFiles(int $caseId, string $lang)
    {

        $this->logInfo("[data: " . json_encode(["caseId" => $caseId, "lang" => $lang]) . "]");

        $response = [];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT") . "/" . $caseId . "/unsigned_booklet?language=" . $lang,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en',
                'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
                'Content-Type: application/json'
            ),
        ));

        $resp = curl_exec($curl);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl), true);
            $this->logError("[response:] [data: " . json_encode($resp) . "]");
        } else {
        }

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl));
            $this->logError("[response:] [data: " . json_encode($resp) . "]");
            $response['error'] = true;
            $response['info'] = curl_error($curl);
        } else {
            $response['error'] = false;
            $response['file'] = $resp;
            $this->logInfo("[response data: downloaded pdf file]");
        }

        curl_close($curl);

        return $response;
    }
}
