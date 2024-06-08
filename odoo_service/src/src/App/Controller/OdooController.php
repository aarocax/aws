<?php 

namespace METRIC\App\Controller;

use Ripcord\Ripcord;
use Ripcord\Exceptions\TransportException;
use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Service\Emailer;
use METRIC\App\Service\Utils;
use METRIC\App\Exception\OdooException;
use METRIC\App\Logger\Logger;

class OdooController implements ControllerInterface
{

  private $settings = [];
  private $response = [];
  private $logger;
  
  public function __construct()
  {
    $this->settings = [
      "db" => AppConfig::getInstance()->getValue("ODOO_DB"),
      "odoo_user" => AppConfig::getInstance()->getValue("ODOO_USER"),
      "api_endpoint" => AppConfig::getInstance()->getValue("ODOO_ENDPOINT"),
      "api_key" => AppConfig::getInstance()->getValue("ODOO_API_KEY"),
    ];
    $this->logger = new Logger;
  }

  public function read(array $data)
  {
    $this->response['error'] = false;
    $this->response['info'] = "";
    return $this->response;
  }

  public function send(array $data)
  {
    $this->logger->info(json_encode($data));
    $this->response["contact_id"] = $this->sendData($this->prepareData($data));
    $this->response['error'] = !is_numeric($this->response["contact_id"]);
    $this->response['info'] = "";
    return $this->response;
  }

  public function redirectUrl(array $data)
  {
    $this->logger->info($data[0]);

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
      $this->logger->error(curl_error($curl));
      $this->logger->error("[response read:] [data: " . $response . "]");
      $this->response['error'] = true;
    } else {
      $this->logger->info("[odoo links response: ]");
      $this->logger->info($response);
      $this->logger->info("odoo links response info: ");
      $this->logger->info($responseInfo);
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
    $this->logger->info(json_encode($preparedData));
    try {

      $models = Ripcord::client($this->settings["api_endpoint"] . "/xmlrpc/2/common");

      $models->_throwExceptions = true;

      try {
        //Authenticate the credentials
        $uid = $models->authenticate($this->settings["db"], $this->settings["odoo_user"], $this->settings["api_key"], array());  
      } catch ( TransportException $e ) {
        $this->logger->error($e->getMessage());
      }
      

      if (!$uid) {
        throw new OdooException("Odoo: No se ha obtenido el user ID");
      }

      $models = Ripcord::client($this->settings["api_endpoint"] . "/xmlrpc/2/object");

      $newContactId = $models->execute_kw(
        $this->settings["db"], $uid, $this->settings["api_key"],
        'res.partner',
        'create', // Function name
        array( $preparedData )
      );

      if (!$newContactId || is_array($newContactId)) {
        throw new OdooException("Odoo: Error al crear el nuevo contacto");
      }

      $this->logger->info("[response contact_id: " . $newContactId . "]");

      $response = $newContactId;
      
    } catch (\Exception $e) {

      $this->logger->error($e->getMessage());
      $response = false;
    }

    return $response;
  }

  private function prepareData(array $data)
  {

    $date = explode("/", $data["birth_date"]);

    //obtener dial number
    $country_phone_code = "";
    foreach (Utils::getCountriesList() as $key => $country) {
      if ($country["dial_code"] == $data['mobile_phone_prefix']) {
        $country_phone_code = $country["dial_number"];
      }
    }

    return [
      'bbva_title' => (array_key_exists('title_salutation', $data)) ? $data['title_salutation'] : '',
      'first_name' => (array_key_exists('first_name', $data)) ? $data['first_name'] : '',
      'name' => (array_key_exists('last_name', $data)) ? $data['last_name'] : '',
      'email' => (array_key_exists('email', $data)) ? $data['email'] : '',
      "birth_date" => $date[2] . "-" . $date[1] . "-" . $date[0],
      "birth_country" => (array_key_exists('country_birth', $data)) ? Utils::getCountryByCode($data['country_birth']) : '',
      "nationality" => (array_key_exists('nationality', $data)) ? Utils::getCountryByCode($data['nationality']) : '',
      "other_nationality" => (array_key_exists('nationality_one', $data)) ? Utils::getCountryByCode($data['nationality_one']) : '',
      "tax_country" => (array_key_exists('tax_residence_country', $data)) ? Utils::getCountryByCode($data['tax_residence_country']) : '',
      "local_tax_id_number" => (array_key_exists('tax_residence_country', $data)) ? $data['local_tax_identification_number'] : '',
      "reason_no_tax_id_number" => (array_key_exists('reason_d_explanation', $data)) ? Utils::getNoLocalTaxIdentificationNumber($data['no_local_tax_identification_number'], "es") : '',
      "mobile" => (array_key_exists('mobile_phone', $data)) ? $data['mobile_phone'] : '',
      "country_code" => $country_phone_code,
      "occupation" => (array_key_exists('occupation', $data)) ? Utils::getOccupation($data['occupation']) : '',
      "linkedin_url" => (array_key_exists('linkedin_profile', $data)) ? "https://www.linkedin.com" . $data['linkedin_profile'] : '',
      "company" => (array_key_exists('employer_name', $data)) ? $data['employer_name'] : '',
      "business_industry" => (array_key_exists('nature_of_bussiness', $data)) ? Utils::getNatureOfBussiness($data['nature_of_bussiness']) : '',
      "job_position" => (array_key_exists('current_job_position', $data)) ? Utils::getCurrentJobPosition($data['current_job_position']) : '',
      "job_title" => (array_key_exists('job_title', $data)) ? $data['job_title'] : '',
      "currency" => (array_key_exists('base_currency', $data)) ? $data['base_currency'] : '', // Base currenciy
      "annual_gross_income" => (array_key_exists('gross_annual_income', $data)) ? Utils::getGrossAnnualIncome($data['gross_annual_income']) : '', //What is your gross annual income?
      "planned_investment" => (array_key_exists('planned_investment', $data)) ? Utils::getPlannedInvestment($data['planned_investment']) : '', // Planned Investment
      "last_employer" => (array_key_exists('pep_name', $data)) ? $data['pep_name'] : '', // nombre pep
      "last_business_industry" => (array_key_exists('pep_function_occupied', $data)) ? $data['pep_function_occupied'] : '', // cargo pep
      "last_job_position" => (array_key_exists('pep_term', $data)) ? $data['pep_term'] : '', // periodo de tiempo pep
      "promo_code" => (array_key_exists('promotional_code', $data)) ? $data['promotional_code'] : '',
      "promotion_code" => (array_key_exists('promotional_code', $data)) ? $data['promotional_code'] : '',
      "referal_email" => (array_key_exists('email_refer_us_a_friend', $data)) ? $data['email_refer_us_a_friend'] : '',
      "hobbies" => (array_key_exists('hobbies', $data)) ? $data['hobbies'] : '',
      "street" => (array_key_exists('street_address', $data)) ? $data['street_address'] : '',
      "city" => (array_key_exists('city', $data)) ? $data['city'] : '',
      "zip" => (array_key_exists('post_code', $data)) ? $data['post_code'] : '',
      "launch_internal_note" => (array_key_exists('data_first_step_form', $data)) ? $data['data_first_step_form'] : '',
    ];
  }

}
