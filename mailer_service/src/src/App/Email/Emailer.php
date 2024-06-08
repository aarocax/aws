<?php

namespace METRIC\App\Email;

use PHPMailer\PHPMailer\PHPMailer;
use METRIC\App\Config\AppConfig;
use METRIC\App\Logger\Logger;

class Emailer
{
  private $mail;
  private $logger;
  private $response;

  public function __construct()
  {

    $this->logger = new Logger;

    $this->mail = new PHPMailer;
    $this->mail->isSMTP();
    $this->mail->IsHTML(true);
    $this->mail->CharSet = 'utf-8';
    $this->mail->SMTPDebug = 0; // for debug set = 2
    $this->mail->Host = AppConfig::getInstance()->getValue("SMTP_HOST");
    $this->mail->Port = AppConfig::getInstance()->getValue("SMTP_PORT");
    $this->mail->SMTPAuth = (bool)AppConfig::getInstance()->getValue("SMTP_AUTH");
    $this->mail->SMTPSecure = AppConfig::getInstance()->getValue("SMTP_SECURE");
    $this->mail->Username = AppConfig::getInstance()->getValue("SMTP_USER_NAME");
    $this->mail->Password = AppConfig::getInstance()->getValue("SMTP_PASSWORD");
    $this->mail->setFrom(AppConfig::getInstance()->getValue("EMAIL_FROM"), AppConfig::getInstance()->getValue("EMAIL_FROM_NAME"));
    $this->mail->addReplyTo(AppConfig::getInstance()->getValue("EMAIL_REPLY_TO"), AppConfig::getInstance()->getValue("EMAIL_FROM_NAME"));
    $this->mail->Subject = "";
    $this->mail->Body    = "";
  }

  public function sendMail(string $to, string $subject = null, string $body = null, array $cc = null, string $userName = null, string $email_reply_to = null): bool
  {
    $this->mail->clearAddresses();
    $this->mail->clearQueuedAddresses('to');
    $this->mail->clearQueuedAddresses('cc');
    $this->mail->clearQueuedAddresses('bcc');
    $this->mail->clearCCs();
    $this->mail->clearBCCs();
    $this->mail->clearAllRecipients();
    $this->mail->clearCustomHeaders();
    $this->mail->clearAttachments();
    $this->mail->clearReplyTos();
    $this->mail->addReplyTo(AppConfig::getInstance()->getValue("EMAIL_REPLY_TO"), AppConfig::getInstance()->getValue("EMAIL_FROM_NAME"));

    $this->mail->addBCC($to, $userName);

    $this->mail->Debugoutput = function ($str, $level) {
      static $logging = true;
      if ($logging === false && str_contains($str, 'SERVER -> CLIENT')) {
        $logging = true;
      }
      if ($logging) {
        error_log("debug level $level; message: $str");
      }
      if (str_contains($str, 'SERVER -> CLIENT: 354')) {
        $logging = false;
      }
    };

    if (is_array($cc)) {
      if (!empty($cc)) {
        foreach ($cc as $key => $email) {
          $this->mail->addCC($email);
        }
      }
    }

    if (filter_var($email_reply_to, FILTER_VALIDATE_EMAIL)) {
      $this->mail->clearReplyTos();
      $this->mail->addReplyTo($email_reply_to, AppConfig::getInstance()->getValue("EMAIL_FROM_NAME"));
    }

    $this->mail->Subject = ($subject) ? $subject : $this->mail->Subject;
    $this->mail->Body = ($body) ? $body : $this->mail->Body;

    if (!$this->mail->send()) {
      $this->logger->error($this->mail->ErrorInfo);
      return false;
    } else {
      $this->logger->info("[" . $_SERVER['REQUEST_URI'] . "]" . " Email enviado");
      return true;
    }
  }
}
