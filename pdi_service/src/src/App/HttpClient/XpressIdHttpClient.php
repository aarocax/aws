<?php

namespace METRIC\App\HttpClient;

use METRIC\App\Controller\BaseController;
use stdClass;
use METRIC\App\Config\AppConfig;

class XpressIdHttpClient extends BaseController
{
    const TIMEOUT = 60;

    public function __construct()
    {
        parent::__construct();
    }

    public function getToken(array $data): string
    {
        $this->logInfo("xpressid preparedData:   " . json_encode($data));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, AppConfig::getInstance()->getValue("XPRESSID_ENDPOINT"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->logError(curl_error($ch), true);
            $this->logError($response, true);
        } 

        curl_close($ch);
        return $response;
    }

}
