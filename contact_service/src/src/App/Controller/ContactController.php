<?php 

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Config\AppConfig;
use METRIC\App\Service\Utils;
use METRIC\App\Email\Emailer;
use METRIC\App\Logger\Logger;


class ContactController implements ControllerInterface
{

  private $emailer;
  private $logger;

  public function __construct()
  {
    $this->emailer = new Emailer();
    $this->logger = new Logger;
  }

  public function read(array $data)
  {
  }

  public function send(array $data)
  {

    $data = $this->prepareData($this->sanitizeContactPostDataEntryArray($data));

    $message = $this->prepareMessage($data);

    $emailResult = $this->emailer->sendMail(
      AppConfig::getInstance()->getValue("EMAIL_CONTACT_TO"),
      "Contacto desde bbva.ch",
      $message
    );

    $response = [];

    if(!$emailResult) {
      $response['info'] = "";
      $response['error'] = true;
    } else {
      $response['info'] = "";
      $response['error'] = false;
    }

    return $response;
  }

  public function update(array $data, int $caseId=0, bool $prepare = true)
  {
  }

  private function sanitizeContactPostDataEntryArray($post)
  {
    return [
      "title_salutation" => (array_key_exists("title_salutation", $post)) ? filter_var($post["title_salutation"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "first_name" => (array_key_exists("first_name", $post)) ? filter_var($post["first_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "last_name" => (array_key_exists("last_name", $post)) ? filter_var($post["last_name"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "email" => (array_key_exists("email", $post)) ? filter_var($post["email"], FILTER_VALIDATE_EMAIL) : "",
      "phone_prefix" => (array_key_exists("phone_prefix", $post)) ? filter_var($post["phone_prefix"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "phone" => (array_key_exists("phone", $post)) ? filter_var($post["phone"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "mobile_phone_prefix" => (array_key_exists("mobile_phone_prefix", $post)) ? filter_var($post["mobile_phone_prefix"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "mobile_phone" => (array_key_exists("mobile_phone", $post)) ? filter_var($post["mobile_phone"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "local_tax_identification_number" => (array_key_exists("local_tax_identification_number", $post)) ? filter_var($post["local_tax_identification_number"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "nationality" => (array_key_exists("nationality", $post)) ? filter_var($post["nationality"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "country_you_live_in" => (array_key_exists("country_you_live_in", $post)) ? filter_var($post["country_you_live_in"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "base_currency" => (array_key_exists("base_currency", $post)) ? filter_var($post["base_currency"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "planned_investment" => (array_key_exists("planned_investment", $post)) ? filter_var($post["planned_investment"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "advised_portfolio" => (array_key_exists("advised_portfolio", $post)) ? filter_var($post["advised_portfolio"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "managed_portfolio" => (array_key_exists("managed_portfolio", $post)) ? filter_var($post["managed_portfolio"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
      "discretional_portfolio" => (array_key_exists("discretional_portfolio", $post)) ? filter_var($post["discretional_portfolio"], FILTER_CALLBACK, ['options' => [$this, 'sanitize_input']]) : "",
    ];
  }

  private function prepareData(array $data)
  {
    return [
      "title_salutation" => (array_key_exists('title_salutation', $data)) ? $data['title_salutation'] : '',
      'first_name' => (array_key_exists('first_name', $data)) ? $data['first_name'] : '',
      'last_name' => (array_key_exists('last_name', $data)) ? $data['last_name'] : '',
      'email' => (array_key_exists('email', $data)) ? $data['email'] : '',
      "phone_prefix" => (array_key_exists('phone_prefix', $data)) ? $data['phone_prefix'] : '',
      "phone" => (array_key_exists('phone', $data)) ? $data['phone'] : '',
      "mobile_phone_prefix" => (array_key_exists('mobile_phone_prefix', $data)) ? $data['mobile_phone_prefix'] : '',
      "mobile_phone" => (array_key_exists('mobile_phone', $data)) ? $data['mobile_phone'] : '',
      "local_tax_identification_number" => (array_key_exists('local_tax_identification_number', $data)) ? $data['local_tax_identification_number'] : '',
      "nationality" => (array_key_exists('nationality', $data)) ? Utils::getCountryByCode($data['nationality']) : '',
      "country_you_live_in" => (array_key_exists('country_you_live_in', $data)) ? Utils::getCountryByCode($data['country_you_live_in']) : '',
      "base_currency" => (array_key_exists('base_currency', $data)) ? $data['base_currency'] : '', // Base currenciy
      "planned_investment" => (array_key_exists('planned_investment', $data)) ? $data['planned_investment'] : '', // Planned Investment
      "advised_portfolio" => (array_key_exists("advised_portfolio", $data)) ? $data['advised_portfolio'] : 'No',
      "managed_portfolio" => (array_key_exists("managed_portfolio", $data)) ? $data['managed_portfolio'] : 'No',
      "discretional_portfolio" => (array_key_exists("discretional_portfolio", $data)) ? $data['discretional_portfolio'] : 'No',
    ];
  }

  private function prepareMessage(array $data)
  {
    return <<<EOT

    <strong>title:</strong> {$data['title_salutation']}<br>
    <strong>first name:</strong> {$data['first_name']}<br>
    <strong>last name:</strong> {$data['last_name']}<br>
    <strong>email:</strong> {$data['email']}<br>
    <strong>phone prefix:</strong> {$data['phone_prefix']}<br>
    <strong>phone:</strong> {$data['phone']}<br>
    <strong>nationality:</strong> {$data['nationality']}<br>
    <strong>country you live in:</strong> {$data['country_you_live_in']}<br>
    <strong>base currency:</strong> {$data['base_currency']}<br>
    <strong>planned investment:</strong> {$data['planned_investment']}<br>

EOT;
  }

  private function sanitize_input($value) {
    return preg_replace('/[!@#%^*()$\=\[\]{};:"\|<>?~]/', '', $value);
  }

}