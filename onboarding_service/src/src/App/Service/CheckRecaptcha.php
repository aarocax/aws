<?php 

namespace METRIC\App\Service;

class CheckRecaptcha
{
  
  private $response = [];

  public static function check(string $token, string $recaptchaSecretKey): array
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $recaptchaSecretKey, 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $arrResponse = json_decode($response, true);
    if($arrResponse["success"] == '1' && $arrResponse["score"] >= 0.5) {
      return ["error" => false, "info" => $response];
    } else {
      return ["error" => true, "info" => $response];
    }
  }
}