<?php 

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Service\Utils;
use METRIC\App\Controller\BaseController;


class AtfinityController extends BaseController implements ControllerInterface
{

  private $settings = [];
  private $response = [];


  public function __construct()
  {

    parent::__construct();

    $this->settings = [
      "process_id" => AppConfig::getInstance()->getValue("ATFINITY_PROCESS_ID"),
      "api_key" => AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
      "api_endpoint" => AppConfig::getInstance()->getValue("ATFINITY_ENDPOINT"),
      "owner_email" => AppConfig::getInstance()->getValue("ATFINITY_OWNER_MAIL"),
      "proof_id_ng_signed_document" => AppConfig::getInstance()->getValue("PROOF_ID_NG_SIGNED_DOCUMENT"),
      "proof_id_ng_boidas_pic1" => AppConfig::getInstance()->getValue("PROOF_ID_NG_BOIDAS_PIC1"),
      "proof_id_ng_boidas_pic2" => AppConfig::getInstance()->getValue("PROOF_ID_NG_BOIDAS_PIC2"),
      "proof_id_ng_boidas_pic3" => AppConfig::getInstance()->getValue("PROOF_ID_NG_BOIDAS_PIC3"),
    ];

  }

  public function read(array $data)
  {
    $this->logInfo(json_encode($data));

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] ."/" . $data["atfinity_case_id"],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'AUTHORIZATION: Api-Key ' . $this->settings["api_key"],
        'Accept: application/json',
        'Accept-Language: en'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response read:] [data: " . $response . "]");
      $this->response['error'] = true;
    } else {
      $resp = json_decode($response);
      $this->logInfo("[response case_id: " . $resp->data->id . "]");
      $this->response['error'] = false;
    }

    curl_close($curl);
    
    $this->response['info'] = $response;

    return $this->response;
  }

  public function send(array $data)
  {
    $this->logInfo(json_encode($data));

    $this->logInfo("check age: " . $this->checkAge($data["birth_date"]));

    if ($this->checkAge($data["birth_date"]) == false) {
      $this->response['error'] = true;
      $this->response['info'] = "";
      $this->logInfo("[edad no vaĺida:]");
    } else {
      $this->response = $this->sendData($this->prepareData($data));
      $this->response = $this->checkAtfinityCreateCaseResponse($this->response);  
    }
    
    return $this->response;
  }

  public function update(array $data, int $caseId=0, bool $prepare = true)
  {
    $this->logInfo(json_encode($data));
    $preparedData = ($prepare) ? $this->prepareUpdateData($data) : $data;
    $this->response = $this->updateData(($caseId > 0) ? $caseId : $data["db"]["atfinity_case_id"], $preparedData);
    $this->response = $this->checkAtfinityCreateCaseResponse($this->response);
    return $this->response;
  }

  /*
   * Comprueba si la repuesta del servicio Atfinity ha sido
   * exitosa y contiene un case_id.
   * Retorna un array con información sobre el caso y si hay error o no
   */
  public function checkAtfinityCreateCaseResponse(string $curlResponse)
  {

    $resp = json_decode($curlResponse);
    $response = [];

    $response['error'] = ($this->existDataId($resp)) ? false : true ;
    $response['info'] = ($response['error']) ? $resp : ""; // se registra la respuesta si hay error
    $response['case_id'] = ($response['error']) ? "" : $resp->data->id;
    $response['instance_id'] = ($response['error']) ? "" : $this->getInstanceId($resp);

    return $response;
  }

  /*
   * Prepara los datos para dar de alta un caso en Atfinity en un array
   * con los nombres de los campos en Atfinity
   */
  public function prepareData(array $data)
  {

    $date = explode("/", $data["birth_date"]);

    //obtener dial number
    $country_phone_code = "";
    foreach (Utils::getCountriesList() as $key => $country) {
      if ($country["dial_code"] == $data['mobile_phone_prefix']) {
        $country_phone_code = $country["dial_number"];
      }
    }

    $atfinityFields = [
      "process_id" => $this->settings["process_id"],
      "owner_email" => $this->settings["owner_email"],
      "predefined_objects" => [
        [
          "ontology" => "Person",
          "roles" => ["AccountHolder"],
          "salutation" => filter_var($_POST["title_salutation"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "first_name" => mb_convert_case(filter_var($data["first_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]), MB_CASE_TITLE, 'UTF-8'),
          "last_name" => mb_convert_case(filter_var($data["last_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]), MB_CASE_TITLE, 'UTF-8'),
          "email" => filter_var($data["email"], FILTER_VALIDATE_EMAIL),
          "ng_phone" => filter_var($data["mobile_phone_prefix"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "mobile" => filter_var($data["mobile_phone"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "date" => $date[2] . "-" . $date[1] . "-" . $date[0],
          "country_birth" => filter_var($data['country_birth'], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_nationality" => filter_var($data["nationality"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_nationality2" => (isset($data["nationality_one"]) && $data["nationality_one"] != "") ? filter_var($data["nationality_one"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : null,
          "ngcountry_live" => filter_var($data["country_you_live_in"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "domicile" => filter_var($data["street_address"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "zip_code" => filter_var($data["post_code"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "city" => filter_var($data["city"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          // Si la residencia fiscal coincide con la de domicilio, se envía el país de domicilio. Si no el de residencia fiscal.
          "ng_tax_country1" => (isset($data["tax_country_residence"]) && $data["tax_country_residence"] == "1") ? filter_var($data["country_you_live_in"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : filter_var($data["tax_residence_country"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_tax_number1" => filter_var($data["local_tax_identification_number"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "linkedin" => (isset($data["linkedin_profile"]) && $data["linkedin_profile"] != "") ? "https://www.linkedin.com" . filter_var($data["linkedin_profile"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : null,
          "company" => filter_var($data["employer_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ngoccupation" => filter_var($data["occupation"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_industry" => (isset($data["nature_of_bussiness"]) && $data["nature_of_bussiness"] != "") ? filter_var($data["nature_of_bussiness"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : null,
          "pep" => (isset($data["important_public_function"]) && $data["important_public_function"] == "yes") ? true : false,
          "pep_term" => filter_var($data["pep_term"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "pep_name" => filter_var($data["pep_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "pep_position" => filter_var($data["pep_function_occupied"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_clientbbva" => filter_var($data["are_you_a_bbva_client"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_income_2" => $data["source_of_your_personal_wealth"], // What is(are) the source(s) of your personal wealth?
          "ng_currency" => filter_var($data["base_currency"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),      // Base currenciy
          "ng_worth" => filter_var($data["what_is_your_total_net_worth"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_inoutflow" => filter_var($data["how_many_cash_inflows_and_outflows_do_you_foresee_in_one_year_in_the_account"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "ng_promcode" => (isset($data["promotional_code"]) && $data["promotional_code"] != "") ? filter_var($data["promotional_code"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : null,
          "web_device" => filter_var($data["web_device"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "web_browser" => filter_var($data["web_browser"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "web_language" => filter_var($data["web_language"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "web_session_time" => filter_var($data["web_session_time"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "web_time" => filter_var($data["web_time"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),
          "web_date" => date('d-m-Y', strtotime("now")),
          "boidas_status" => filter_var($data["boidas_status"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]),

          // campos % distribución patrimonio
          "wealth_distr1" => filter_var($data["percentage_financial_assets"], FILTER_SANITIZE_NUMBER_FLOAT),
          "wealth_distr2" => filter_var($data["percentage_digital_assets"], FILTER_SANITIZE_NUMBER_FLOAT),
          "wealth_distr3" => filter_var($data["percentage_immovables"], FILTER_SANITIZE_NUMBER_FLOAT),
          "wealth_distr4" => filter_var($data["percentage_other_non_financial_assets"], FILTER_SANITIZE_NUMBER_FLOAT),

          //campos ingresos brutos anuales
          "ngsource_wealth1" => (isset($data["gross_annual_income_salary"])) ? filter_var($data["gross_annual_income_salary_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth2" => (isset($data["gross_annual_income_business"])) ? filter_var($data["gross_annual_income_business_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth3" => (isset($data["gross_annual_investing_income"])) ? filter_var($data["gross_annual_investing_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth4" => (isset($data["gross_annual_investing_digital_assets_income"])) ? filter_var($data["gross_annual_investing_digital_assets_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth5" => (isset($data["gross_annual_rental_income"])) ? filter_var($data["gross_annual_rental_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth6" => (isset($data["gross_annual_pension_income"])) ? filter_var($data["gross_annual_pension_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth7" => (isset($data["gross_annual_insurance_income"])) ? filter_var($data["gross_annual_insurance_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth8" => (isset($data["gross_annual_patents_income"])) ? filter_var($data["gross_annual_patents_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
          "ngsource_wealth9" => (isset($data["gross_annual_other_income"])) ? filter_var($data["gross_annual_other_income_range"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "source_in0",
        ]
      ]
    ];

    /*
    if ($data['occupation'] === 'occup5' || $data['occupation'] === 'occup3') {
      $atfinityFields["predefined_objects"]['past_company'] = (isset($data["employer_name"]) && $data["employer_name"] != "") ? $data["employer_name"] : null;
      $atfinityFields["predefined_objects"]['past_ng_industry'] = (isset($data["nature_of_bussiness"]) && $data["nature_of_bussiness"] != "") ? $data["nature_of_bussiness"] : null;

      $atfinityFields["predefined_objects"]['company'] = null;
      $atfinityFields["predefined_objects"]['ng_industry'] = null;
    }
    */

    return $atfinityFields;
  }

  public function getData($caseId)
  {

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => sprintf("%s/%d", $this->settings["api_endpoint"], $caseId),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . $this->settings["api_key"]
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response getData:] [data: " . $response . "]");
    } else {
      $resp = json_decode($response);
      $this->logInfo("[response case_id: " . $resp->data->id . "]");
    }

    curl_close($curl);

    return json_decode($response);
  }

  public function sendData(array $preparedData)
  {
    $encodedData = json_encode($preparedData);

    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $encodedData,
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . $this->settings["api_key"],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response sendData:] [data: " . $response . "]");
    } else {
      $resp = json_decode($response);
      $this->logInfo("[resp: ");
      $this->logInfo($resp);
      if(property_exists( $resp , "data" )){
        $this->logInfo("[response case_id: " . $resp->data->id . "]");
      } else {
        $this->logError($resp);
      }
      
    }

    curl_close($curl);

    return $response;
  }

  /*
   * Prepara los campos para actualizar un caso en Atfinity. 
   * Estos campos se actualizan cuando el formulario se inicia
   * en paso 3 y se actualizan solo los campos de nacionalidad.
   */
  public function prepareUpdateData(array $data)
  {

    $fields['fields'] = [];

    if (!empty($data['nationality_retargeting'])) {
      $fields['fields'][] = [
        "instance_id" => $data["db"]["atfinity_instance_id"], 
        "information_key" => "ng_nationality",
        "value" => $data['nationality_retargeting']
      ];
    };

    if (!empty($data['nationality_one_retargeting'])) {
      $fields['fields'][] = [
        "instance_id" => $data["db"]["atfinity_instance_id"], 
        "information_key" => "ng_nationality2",
        "value" => $data['nationality_one_retargeting']
      ];
    }

    return $fields;
  }

  private function updateData($case_id, $preparedData)
  {

    $encodedData = json_encode($preparedData);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] ."/" . $case_id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PATCH',
      CURLOPT_POSTFIELDS =>$encodedData,
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response:] [data: " . $response . "]");
    } else {
      $resp = json_decode($response);
      $this->logInfo("[response case_id: " . $resp->data->id . "]");
    }

    curl_close($curl);

    return $response;
  }

  /*
   * Obtiene el id de la instancia del caso creado en atfinity
   */
  public function getInstanceId(object $atfinityCreateResponse)
  {
    $instance_id = "";
    foreach ($atfinityCreateResponse->data->fields as $key => $value) {
      if ($value->key == "web_behaviour") {
        $instance_id = $value->instance_id;
      }
    }

    return $instance_id;
  }

  /*
   * Examina la respuesta de atfinity para encontrar el id del caso
   * creado o actualizado.
   */
  public function existDataId($response)
  {
    $resp = false;
    if (is_object($response)) {
      if (property_exists($response, "data")) {
        $resp = (property_exists($response->data, "id")) ? true : false;
      }
    }
    return $resp;
  }

  public function getFiles(int $caseId, string $lang) {

    $this->logInfo("[data: " . json_encode(["caseId" => $caseId, "lang" => $lang]) . "]");

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . "/" . $caseId . "/unsigned_booklet?language=" . $lang,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        //'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
        'Content-Type: application/json'
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
      $this->logInfo("[response data: downloaded pdf file]");
    }

    curl_close($curl);

    return $this->response;
  }

  public function getProofIds(int $caseId) {

    $this->logInfo("[data: " . json_encode(["case_id" => $caseId]) . "]");

    if (is_int($caseId)) {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->settings["api_endpoint"] ."/" . $caseId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => self::TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'AUTHORIZATION: Api-Key ' . $this->settings["api_key"],
          'Accept: application/json',
          'Accept-Language: en'
        ),
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)){
        $this->logError(curl_error($curl));
        $this->response['error'] = true;
        $this->response['info'] = curl_error($curl);
      } else {
        $this->response['error'] = false;
        $res = json_decode($response);
        
        $data = [];
        if ($res && property_exists($res, "data") && $res->data && property_exists($res->data, "proofs") && is_array($res->data->proofs)) {
          foreach ($res->data->proofs as $proofs) {

            switch($proofs->document_user_readable_id) {
              case $this->settings['proof_id_ng_signed_document']:
              case $this->settings['proof_id_ng_boidas_pic1']:
              case $this->settings['proof_id_ng_boidas_pic2']:
              case $this->settings['proof_id_ng_boidas_pic3']:
                $data[$proofs->document_user_readable_id] = $proofs->id;
                break;
            }
          }
        }
        $this->response['data'] = $data;
        $this->logInfo("[response data: " . json_encode($data) . "]");
      }

      curl_close($curl);
    } else {
      $this->response['error'] = true;
      $this->response['info'] = "Error caseId";
    }

    return $this->response;
  }

  public function uploadFileWithId(int $caseId, int $id, string $name, string $file, bool $image = true) {

    $this->logInfo("[data: " . json_encode(["case_id" => $caseId, "id" => $id, "name" => $name]) . "]");

    if (is_int($caseId) && is_int($id) && !empty($file)) {

      $curl = curl_init();

      $fileBin = ($image) ? base64_decode($file) : $file;
      $filesize = strlen($fileBin);

      $temp = tmpfile();
      $meta_data = stream_get_meta_data($temp);
      $tempName = $meta_data["uri"];
      file_put_contents($tempName, $fileBin);
      $mime = mime_content_type($tempName);
      $postfields = array("file" => new \CurlFile($tempName, $mime, $name), "name" => $name);

      curl_setopt_array($curl, array(
        CURLOPT_URL => sprintf("%s/%d/proofs/%d", $this->settings["api_endpoint"], $caseId, $id),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => self::TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $postfields,
        // CURLOPT_INFILESIZE => $filesize,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Accept-Language: en',
          'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
          'Content-Type: multipart/form-data',
        ),
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)){
        $this->logError(curl_error($curl));
        $this->logError("[response:] [data: " . json_encode($response) . "]");
        $response = "error";
      } else {
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->logInfo("[response data: uploaded file]");
        $this->logInfo("[response:] [data: " . json_encode($response) . "]");

        if ($http_code === 200) {
          $response = "ok";
        } else {
          $response = "error";
        }
      }

      curl_close($curl);
    } else {
      $response = "error";
    }

    return $response;
  }

  public function uploadFile(int $caseId, string $name, string $file, bool $image = true) {

    $this->logInfo("[data: " . json_encode(["case_id" => $caseId, "name" => $name]) . "]");

    if (is_int($caseId) && !empty($file)) {

      $curl = curl_init();

      $fileBin = ($image) ? base64_decode($file) : $file;
      $filesize = strlen($fileBin);

      $temp = tmpfile();
      $meta_data = stream_get_meta_data($temp);
      $tempName = $meta_data["uri"];
      file_put_contents($tempName, $fileBin);
      $mime = mime_content_type($tempName);
      $postfields = array("file" => new \CurlFile($tempName, $mime, $name), "name" => $name);

      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->settings["api_endpoint"] . "/" . $caseId . "/proofs",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => self::TIMEOUT,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postfields,
        // CURLOPT_INFILESIZE => $filesize,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Accept-Language: en',
          'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
          'Content-Type: multipart/form-data',
        ),
      ));

      $response = curl_exec($curl);

      if (curl_errno($curl)){
        $this->logError(curl_error($curl));
        $this->logError("[response:] [data: " . json_encode($response) . "]");
        $response = "error";
      } else {
        $this->logInfo("[response data: uploaded file]");
        $response = "ok";
      }

      curl_close($curl);
    } else {
      $response = "error";
    }

    return $response;
  }

  public function stateTransition(int $caseId, int $transitionId) {

    //$fields = ["transition_id" => 227];
    $fields = ["transition_id" => $transitionId];

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] . '/' . $caseId .'/transition',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($fields),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
        'Content-Type: application/json'
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
      $this->response['info'] = $response;
    }

    curl_close($curl);

    return $this->response;
  }

  public function updateProspectField(int $caseId, int $instanceId, bool $send, string $message)
  {
    
    $fields['fields'][] = [
      "instance_id" => $instanceId, 
      "information_key" => "group_template_senderror",
      "value" => $message
    ];

    $fields['fields'][] = [
      "instance_id" => $instanceId, 
      "information_key" => "group_template_send",
      "value" => $send
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->settings["api_endpoint"] ."/" . $caseId,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => self::TIMEOUT,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PATCH',
      CURLOPT_POSTFIELDS =>json_encode($fields),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Accept-Language: en',
        'AUTHORIZATION: Api-Key ' . AppConfig::getInstance()->getValue("ATFINITY_API_KEY"),
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logError(curl_error($curl));
      $this->logError("[response:] [data: " . $response . "]");
    } else {
      $resp = json_decode($response);
      $this->logInfo("[response case_id: " . $resp->data->id . "]");
    }

    curl_close($curl);

    return $response;
  }
  
  private function setGrossAnnualIncomeValue(int $value)
  {
    switch ($value) {
      case 0:
        return 'gross1';
      case 20:
        return 'gross2';
      case 40:
        return 'gross3';
      case 60:
        return 'gross4';
      // El valor 80 nunca llega, pasa de 60 a 100, lo mantengo por si hay modificaciones
      case 80:
        return 'gross5';
      case 100:
        return 'gross5';
      default:
        return null;
    }
  }

  private function setPlannedInvestmentValue(int $value)
  {
    switch ($value) {
      case 0:
        return 'gross1';
     
      case 20:
        return 'gross2';

      case 40:
        return 'gross3';

      case 60:
        return 'gross4';

      // El valor 80 nunca llega, pasa de 60 a 100, lo mantengo por si hay modificaciones
      case 80:
        return 'gross5';

      case 100:
        return 'gross5';

      default:
        return null;
    }
  }

  private function checkAge(string $birthDate)
  {
    $birthDate = str_replace('/', '-', $birthDate);
    $birthDate = new \DateTime(date('Y-m-d', strtotime($birthDate)));
    $now = new \DateTime('today');
    $age = $birthDate->diff($now)->y;
    return ($age >= 18) ? true : false;
  }

  private function sanitize_input($value) {
    return preg_replace('/[!@#%^*()$\=\[\]{};:"\|<>?~]/', '', $value);
  }

}