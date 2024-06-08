<?php

namespace METRIC\App\Controller;

use METRIC\App\Controller\BaseController;
use METRIC\App\HttpClient\AtfinityHttpClient;
use METRIC\App\Service\SecureToken;
use METRIC\App\Controller\DBController;
use stdClass;

class AtfinityController extends BaseController
{
    private AtfinityHttpClient $atifinityHttpClient;

    public function __construct(AtfinityHttpClient $atfinityHttpClient)
    {
        parent::__construct();
        $this->atifinityHttpClient = $atfinityHttpClient;
    }

    public function getCases(int $page = 1, int $pageSize = 50): array | null
    {
        $cases = $this->atifinityHttpClient->getCases($page, $pageSize);
        return $cases;
    }

    public function getCase(int $caseId): array
    {
        $response = [];

        $case = $this->atifinityHttpClient->getCase($caseId);

        if ($case === null || $case === false) {
            $response['error'] = true;
            $response['risk'] = $case;
            return $response;
        }

        return $response;
    }

    /**
     * getRisk
     *
     * @param  mixed $caseId
     * @return array
     */
    public function getRisk(int $caseId): array
    {
        $case = $this->atifinityHttpClient->getCase($caseId);

        $response['error'] = true;
        $response['risk'] = $case;

        if ($case === null || $case === false) {
            $this->logError($case);
            $response['error'] = true;
            return $response;
        }

        if (gettype($case) === 'string') {
            $case = json_decode($case);
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->logError(json_last_error_msg());
                $response['error'] = true;
                return $response;
            }
        }

        if (gettype($case) === 'object') {
            if (property_exists($case, "data")) {
                $fields = $case->data->fields;
                foreach ($fields as $key => $field) {
                    if ($field->key === 'pdi3_invest_profile') {
                        $response['error'] = false;
                        $response['risk'] = $field->value->label;
                    }
                }
            }
        }

        return $response;
    }

    public function getFiles(int $caseId, string $language)
    {
        $case = $this->atifinityHttpClient->getFiles($caseId, $language);
        return $case;
    }

    public function setURLS(): void
    {
        $page = 0;
        do {
            $page++;
            $cases = $this->getCases($page, 50);
            $casesArray[$page - 1] = $cases["data"];
            $pages = $cases["meta"]["pagination"]["pages"];
        } while ($page < $pages);

        $this->logInfo($casesArray);
        $this->logInfo(json_encode($casesArray));

        if ($casesArray[0] === null) {
            $this->logInfo("no hay casos en estado Waiting for url...");
        }

        foreach ($casesArray[0] as $key => $case) {
            $caseId = $case["id"];
            $token = SecureToken::generateToken((int)$caseId);

            // guardar caso en la BBDD
            $dbData = [
                'atfinity_case_id' => $case["id"],
                'atfinity_instance_id' => $case["outcome_instance_id"],
                'atfinity_instance_fields_id' => '1000',
                'language' => 'es',
                'document_type' => 'id',
                'hash' => $token['hash'],
                'hash_key' => $token['key']
            ];
            $dataBBDD = (new DBController())->insertCaseToAtfinity($dbData);

            // obtener caso
            $case = $this->getCase($caseId);

            $this->logInfo("case: ");
            $this->logInfo(json_encode($case));

            $caseUrl = 'http://www.bbva.ch/pdi/?q=' . $token["hash"];

            // actualizar campo Renew_pdi_url
            foreach ($case->data->fields as $key => $value) {
                if ($value->key == "renew_pdi_url") {
                    $instance_id = $value->instance_id;
                    $information_key = $value->key;
                    $value = $caseUrl;
                    $response = $this->atifinityHttpClient->updateCase($caseId, $instance_id, $information_key, $value);
                    break;
                }
            }

            // transition
            // $this->transition($caseId, 429);

        }
    }

    public function checkToken(string $hash): bool
    {
        $response = (new DBController())->getHashKeyByHash($hash);
        $this->logInfo($response);
        if ($response["error"] === true) {
            $caseId = null;
        } else {
            $caseId = SecureToken::decodeToken($hash, $response["info"]["hash_key"]);
        }
        $this->logInfo("cade ID: " . $caseId);
        return (is_int($caseId) && $caseId >= 0) ? true : false;
    }

    public function updateCase(int $caseId, $fieldInstanceId, $fieldInformationKey, $fieldValue)
    {

        $data = $this->prepareUpdateData($caseId, $fieldInstanceId, $fieldInformationKey, $fieldValue);
        $this->logInfo("update case data: ");
        $this->logInfo($data);

        $case = $this->atifinityHttpClient->updateCase($caseId, $data);

        $response['error'] = true;
        $response['update'] = $case;

        if ($case === null || $case === false) {
            $this->logError($case);
            $response['error'] = true;
            return $response;
        }

        if (gettype($case) === 'string') {
            $case = json_decode($case);
            if (JSON_ERROR_NONE !== json_last_error()) {
                $this->logError(json_last_error_msg());
                $response['error'] = true;
                return $response;
            }
        }
        
        if (gettype($case) === 'object') {
            if (property_exists($case, "data")) {
                $fields = $case->data->fields;
                foreach ($fields as $key => $field) {
                    if ($field->key === 'pdi3_invest_profile') {
                        $response['error'] = false;
                        $response['risk'] = $field->value->label;
                    }
                }
            }
        }

        return $response;
        
    }

    public function transition(int $caseId, int $transitionId)
    {
        return $this->atifinityHttpClient->transition($caseId, $transitionId);
    }

    public function sendData(array $data)
    {
        $this->logInfo($data);
        $this->logInfo('horizonte_inversion: ' . $data['horizonte_inversion']);
        //$pdi3_q01 = $data['horizonte_inversion'];
        //$this->updateCase($caseId, $this->prepareData($data));
        //$this->atifinityHttpClient->updateCase($caseId, $fieldInstanceId, $fieldInformationKey, $fieldValue);
    }

    private function prepareData(array $data): array
    {
        $atfinityFields = [
            'pdi3_q01' => $data['productos_inversion']
        ];

        return $atfinityFields;
    }

    private function prepareUpdateData($caseId, $fieldInstanceId, $fieldInformationKey, $fieldValue): array
    {
        $fields['fields'] = [];

        if (!empty($fieldValue)) {
            $fields['fields'][] = [
                "instance_id" => $fieldInstanceId,
                "information_key" => $fieldInformationKey,
                "value" => $fieldValue
            ];
        };

        return $fields;
    }
}
