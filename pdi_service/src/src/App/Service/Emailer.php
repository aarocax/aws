<?php

namespace METRIC\App\Service;

use METRIC\App\Config\AppConfig;
use METRIC\App\Service\Logger;

class Emailer
{
  private $mail;
  private $response;
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger;
  }

  public function sendMail(string $to, string $subject = null, string $body = null, array $cc = null, string $userName = null, string $email_reply_to = null): bool
  {

    $postData = http_build_query([
      'to' => $to,
      'subject' => $subject,
      'body' => $body,
      'cc' => $cc ? implode(',', $cc) : null,
      'userName' => $userName ? $userName : null,
      'email_reply_to' => $email_reply_to ? $email_reply_to : null
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => AppConfig::getInstance()->getValue("MAILER_SERVICE"),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->logger->error('[Emailer error:] ' . curl_error($curl));
    }

    curl_close($curl);

    return $response;
  }

  public function sendMailRetargeting(string $to, string $subject = null, string $body = null, array $cc = null, string $userName = null, string $email_reply_to = null): bool
  {

    $postData = http_build_query([
      'to' => $to,
      'subject' => $subject,
      'body' => $body,
      'cc' => $cc ? implode(',', $cc) : null,
      'userName' => $userName ? $userName : null,
      'email_reply_to' => $email_reply_to ? $email_reply_to : null
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => AppConfig::getInstance()->getValue("MAILER_SERVICE") . "/retargeting",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
  }
}
