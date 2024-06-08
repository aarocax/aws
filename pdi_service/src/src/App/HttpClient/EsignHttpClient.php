<?php

namespace METRIC\App\HttpClient;

use METRIC\App\Controller\BaseController;
use stdClass;
use METRIC\App\Config\AppConfig;

/**
 * EsignHttpClient
 */
class EsignHttpClient extends BaseController
{

    private $response = [];

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function sendDocument(array $data): string | bool
    {

        $date = new \DateTime();
        $date = $date->format('YmdHis');

        $temp = tmpfile();
        $meta_data = stream_get_meta_data($temp);
        $tempName = $meta_data["uri"];
        file_put_contents($tempName, $data["file"]);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => AppConfig::getInstance()->getValue("ESIGN_ENDPOINT") . '/library_documents',
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
                    'apikey: ' . AppConfig::getInstance()->getValue("ESIGN_API_KEY")
                )
            )
        );

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $this->logError(curl_error($curl));
            $this->logError($response);
        } else {
            $this->logInfo($response);
        }

        curl_close($curl);

        return $response;
    }
}
