<?php 

namespace METRIC\App\Controller;

use METRIC\App\ControllerInterface;
use METRIC\App\Controller\BaseController;
use METRIC\App\Config\AppConfig;
use METRIC\App\Email\Emailer;


class XpressIDController extends BaseController implements ControllerInterface
{

  private static $LITERALES = array(

    'es' => array(
      "continueText" => 'Seguir',
      "iframeButtonQrOnboarding" => 'Continuar en el ordenador',
      "infoReviewImageText" => 'Compruebe que la foto es legible y está bien enfocada',
      "infoReviewBlurImageText" => 'Revise que la imagen sea nítida',
      "manualCaptureText" => "Capturando imagen",
      "manualCaptureTextMobile" => "Capturando imagen",
      "obverseNotFoundText" => "Encaje la parte delantera",
      "reverseNotFoundText" => "Encaje la parte trasera",
      "infoUserDocumentTitle" => '1. Centre tu documento en el marco\n\n 2. Asegúrese de que su documento no muestra brillos ni sombras.\n\n 3. La fotografía se capturará de forma automática.',
      "repeatText" => "Repetir",
      "infoUserSliderButtonText" => "Continuar",
      "infoUserAliveHeader" => "Antes de comenzar, lea estas instrucciones",
      "infoUserAliveButtonText" => "Continuar",
      "iframeErrorDocumentAnversoText" => "El análisis del anverso del documento ha fallado. Por favor, repita el proceso",
      "tooFarText" => "Demasiado lejos",
      "iframeLoaderText" => "El proceso está en marcha. Espere unos instantes…",
      "videoErrorDefault" => "Lo sentimos, ha habido un error al conectar con su cámara",
      "message_alive_light" => "Mueva su cabeza",
      "message_alive_bold" => "LENTAMENTE",
      "stepChallenge" => "Paso",
      "stepOfChallenge" => "de",
      "iframeQrTitle" => "Escanee este código QR con tu Smartphone",
      "iframeEsignRefuseButtonText" => "Rechazar",
      "iframeEsignTitleText" => "Firmar aquí",
      "iframeEsignSignInputDefaultText" => "Nombre o iniciales",
      "iframeEsignSignButtonText" => "Firmar",
      "infoUserAliveTitle" => "Sigua las instrucciones de movimiento",
      "infoUserAliveSubTitle" => '1. Encaje la cara en el marco y espere la cuenta atrás.\n2. Mueva la cabeza lentamente en la dirección que le indiquen las flechas.\n3. Cuando la pantalla le indique que lo ha hecho correctamente, vuelva a mirar al centro.\n4. Continúe realizando los movimientos solicitados hasta completar el proceso.',
      "fitYourFace" => 'Ahora, encaje su cara en el marco y mantengase estable mientras tomamos una foto',
      "restartingErrorText" => 'No se ha podido completar el proceso. Por favor, repita la validación.',
      "center_face" => 'Centre su cara',
      "not_move" => 'Mantengase estable',
      "message_middle_center" => '¡Bien! Vuelva al centro',
      "message_finish_challenge" => '¡Bien!',
      "tooCloseText" => 'Demasiado Cerca'
    ),

    'en' => array(
      "continueText" => 'Continue',
      "iframeButtonQrOnboarding" => 'Continue on desktop',
      'infoReviewImageText' => 'Check that the photo is legible and well focused',
      "infoReviewBlurImageText" => 'Revisa que la imagen sea nítida',
      "manualCaptureText" => "Capturing image",
      "manualCaptureTextMobile" => "Capturing image",
      "obverseNotFoundText" => "fit front",
      "reverseNotFoundText" => "fit back",
      "infoUserDocumentTitle" => '1. Center your document in the frame\n\n 2. Make sure your document has no highlights or shadows.\n\n 3. The photo will be captured automatically.',
      "repeatText" => "Repeat",
      "infoUserSliderButtonText" => "Continue",
      "infoUserAliveHeader" => "Before you start, read these instructions",
      "infoUserAliveButtonText" => "Continue",
      "iframeErrorDocumentAnversoText" => "The analysis of the anverse of the document has failed. Please repeat the process",
      "tooFarText" => "Too Far",
      "iframeLoaderText" => "We are processsing your information, please wait.",
      "videoErrorDefault" => "Sorry, there was an error starting the camera",
      "message_alive_light" => "Move your head",
      "message_alive_bold" => "SLOWLY",
      "stepChallenge" => "Step",
      "stepOfChallenge" => "of",
      "iframeQrTitle" => "Scan this QR code with your Smartphone",
      "iframeEsignRefuseButtonText" => "Cancel",
      "iframeEsignTitleText" => "Sign here",
      "iframeEsignSignInputDefaultText" => "Name or initial",
      "iframeEsignSignButtonText" => "Sign",
      "infoUserAliveTitle" => "Follow the motion instructions",
      "infoUserAliveSubTitle" => '1. Fit your face into the frame and wait for the countdown.\n2. Turn your face in one direction following the arrows.\n3. When the screen tells you that you have done it correctly, look back at the center.\n4. Continue doing all the movements until finishing the process.',
      "fitYourFace" => 'Now, place your face inside the frame and hold steady while we take a photo',
      "restartingErrorText" => 'The process could not be completed. Please repeat the validation.',
      "center_face" => 'Center your face',
      "not_move" => 'Hold steady',
      "message_middle_center" => '¡Great! Look back at the center',
      "message_finish_challenge" => '¡Great!',
      "tooCloseText" => 'Too Close'
    )
  );


  private $emailer;
  private $settings = [];
  private $response = [];
  
  public function __construct()
  {

    parent::__construct();

    $this->settings = [
        "client_id" => AppConfig::getInstance()->getValue("XPRESSID_CLIENT_ID"),
        "client_secret" => AppConfig::getInstance()->getValue("XPRESSID_CLIENT_SECRET"),
        "api_endpoint" => AppConfig::getInstance()->getValue("XPRESSID_ENDPOINT"),
        "mobile_redirect" => AppConfig::getInstance()->getValue("MOBILE_REDIRECT_URL"),
        "mobile_redirect_en" => AppConfig::getInstance()->getValue("MOBILE_REDIRECT_URL_EN"),
    ];
    
    $this->emailer = new Emailer();
  }

  public function read(array $data)
  {
    $this->response['info'] = "";
    return $this->response;
  }

  public function send(array $data)
  {
    $this->logInfo(json_encode($data));
    $this->response = $this->sendData($this->prepareData($data));
    $this->response = $this->checkXpressIDResponse($this->response);
    return $this->response;
  }

  public function update(array $data)
  {
    $this->response['error'] = false;
    $this->response['info'] = "";
    return $this->response;
  }

  private function sendData($preparedData)
  {

    $this->logInfo("xpressid preparedData:   " . json_encode($preparedData));

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->settings['api_endpoint']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $preparedData);

    $response = curl_exec($ch);

    $this->logInfo("xpressid response:   " . $response);

    if (curl_errno($ch)) {
      $this->logError(curl_error($ch));
      $this->logError($response);
    } else {
      $resp = json_decode($response);
      $this->logInfo("access_token: " . $resp->access_token );
    }

    curl_close($ch);

    return $response;

  }

  public function prepareData(array $data)
  {

    $documentType = $data['document_type'];
    $library_document_id = $data['library_document_id'];

    $lang = (isset($data['language']) && isset(self::$LITERALES[$data['language']])) ? $data['language'] : 'es';

    $this->settings["mobile_redirect"] = ($lang == "es") ? $this->settings["mobile_redirect"] : $this->settings["mobile_redirect_en"];

    $disableContinueOnDesktop = "true";
    if (AppConfig::getInstance()->getValue("BOIDAS_USERNAME") !== null && 
      AppConfig::getInstance()->getValue("BOIDAS_USERNAME") === 'anselmo.aroca.ext@metricsalad.com')
    {
      $disableContinueOnDesktop = "false";
    }

    $email = '';
    if (isset($data["atfinity_email"]) && !empty($data["atfinity_email"])) {
      $email = $data["atfinity_email"];
    } else if(isset($data["email"]) && !empty($data["email"])) {
      $email = $data["email"];
    }

    $userName = '';
    if (isset($data["userName"]) && !empty($data["userName"])) {
      $userName = $data["userName"];
    } else if(isset($data["atfinity_first_name"]) && !empty($data["atfinity_first_name"])) {
      $userName = $data["atfinity_first_name"];
    }

    $ret = [
      'client_id' => $this->settings["client_id"],
      'client_secret' => $this->settings["client_secret"],
      'grant_type' => 'client_credentials',
      'data' => '{
        "flowId":"document_selfiealive",
        "videocall": false,
        "selfieChallenge": true,
        "documentType": "' . $documentType . '",
        "scoresConfiguration": {
          "minimumAcceptableAge": 18,
          "modifiers": {
            "ScoreGroup-PhotoAuthenticity": 1,
            "ScoreGroup-PrintAttackTest": 1,
            "ScoreGroup-ReplayAttackTest": 1
          }
        },
        "configData": {
          "continueText": "' . self::$LITERALES[$lang]["continueText"] . '",
          "iframeButtonQrOnboarding": "' . self::$LITERALES[$lang]["iframeButtonQrOnboarding"] . '",
          "iframeQrTitle": "' . self::$LITERALES[$lang]["iframeQrTitle"] . '",
          "iframeQrTitleLine1": " ",
          "iframeQrTitleLine2": " ",
          "iframeInitialColor": "#6754b8",
          "iframeEndColor": "#6754b8",
          "iframeLoaderText": "' . self::$LITERALES[$lang]["iframeLoaderText"] . '",
          "videoErrorDefault": "' . self::$LITERALES[$lang]["videoErrorDefault"] . '",
          "infoAlertSingleSidedDocument": "infoAlertSingleSidedDocument",
          "infoAlertTwoSidedDocument": "infoAlertTwoSidedDocument",
          "infoReviewBlurImageText": "' . self::$LITERALES[$lang]["infoReviewBlurImageText"] . '",
          "infoReviewImageText": "' . self::$LITERALES[$lang]["infoReviewImageText"] . '",
          "manualCaptureText": "' . self::$LITERALES[$lang]["manualCaptureText"] . '",
          "manualCaptureTextMobile": "' . self::$LITERALES[$lang]["manualCaptureTextMobile"] . '",
          "obverseNotFoundText": "' . self::$LITERALES[$lang]["obverseNotFoundText"] . '",
          "reverseNotFoundText" : "' . self::$LITERALES[$lang]["reverseNotFoundText"] . '",
          "passportNotFoundText": "passportNotFoundText",
          "passportMRZError": "passportMRZError",
          "repeatText": "' . self::$LITERALES[$lang]["repeatText"] . '",
          "fontSize": 5,
          "iframeErrorDocumentAnversoText": "' . self::$LITERALES[$lang]["iframeErrorDocumentAnversoText"] . '",
          "tooFarText": "' . self::$LITERALES[$lang]["tooFarText"] . '",
          "message_alive_light": "' . self::$LITERALES[$lang]["message_alive_light"] . '",
          "message_alive_bold": "' . self::$LITERALES[$lang]["message_alive_bold"] . '",
          "showLogo": false,
          "infoUserDocumentMedia": "data:image/png;base64,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",
          "infoUserDocumentTitle": "' . self::$LITERALES[$lang]["infoUserDocumentTitle"] . '",
          "infoUserSliderButtonText": "' . self::$LITERALES[$lang]["infoUserSliderButtonText"] . '",
          "stepChallenge": "' . self::$LITERALES[$lang]["stepChallenge"] . '",
          "stepOfChallenge": "' . self::$LITERALES[$lang]["stepOfChallenge"] . '",
          "infoUserDocumentBackgroundColor": "#ffffff",
          "infoUserDocumentBackgroundColorTop":"#8f7ae5",
          "infoUserDocumentBackgroundColorButton": "#8f7ae5",
          "infoUserDocumentTextColor": "#8f7ae5",
          "sdkBackgroundColorInactive": "#ffffff",
          "borderColorCenteringAidDefault":"#8f7ae5",
          "confirmationDialogLinkTextColor":"#000000",
          "confirmationDialogButtonBackgroundColor":"#8f7ae5",
          "confirmationCaptureButtonBackgroundColor":"#8f7ae5",
          "confirmationDialogButtonTextColor":"#ffffff",
          "confirmationDialogTextColor": "#8f7ae5",
          "detectionMessageTextColor": "#8f7ae5",
          "detectionMessageBackgroundColor": "#c0c0c0",
          "detectionMessageTextColorSelfie": "#8f7ae5",
          "detectionMessageBackgroundColorSelfie": "#c0c0c0",
          "loadingSpinnerColor": "#8f7ae5",
          "loadingSpinnerScreenBackgroundColor": "#ffffff",
          "infoUserAliveHeader": "' . self::$LITERALES[$lang]["infoUserAliveHeader"] . '",
          "infoUserAliveButtonText": "' . self::$LITERALES[$lang]["infoUserAliveButtonText"] . '",
          "infoUserAliveNextButtonColor": "#8f7ae5",
          "infoUserAlivePrevButtonColor": "#8f7ae5",
          "infoUserAliveColorButton": "#8f7ae5",
          "infoUserAliveBackgroundColor": "#ffffff",
          "infoUserAliveHeaderColor": "#8f7ae5",
          "infoUserAliveTitleColor": "#8f7ae5",
          "infoUserAliveSubTitleColor": "#8f7ae5",
          "infoUserAliveTitle": ["' . self::$LITERALES[$lang]["infoUserAliveTitle"] . '"],
          "infoUserAliveSubTitle": ["' . self::$LITERALES[$lang]["infoUserAliveSubTitle"] . '"],
          "stepsChallengeColor": "#8f7ae5",
          "confirmationColorTick": "#8f7ae5",
          "confirmationDialogBackgroundColor": "#ffffff",
          "infoUserSelfieBackgroundColorButton": "#8f7ae5",
          "infoUserSelfieBackgroundColorTop": "#8f7ae5",
          "repeatButtonColor": "#000000",
          "buttonBackgroundColorLight": "#8f7ae5",
          "buttonBackgroundColorDark": "#8f7ae5",
          "iframeEsignRefuseButtonText": "' . self::$LITERALES[$lang]["iframeEsignRefuseButtonText"] . '",
          "iframeEsignTitleText": "' . self::$LITERALES[$lang]["iframeEsignTitleText"] . '",
          "iframeEsignSignInputDefaultText": "' . self::$LITERALES[$lang]["iframeEsignSignInputDefaultText"] . '",
          "iframeEsignSignButtonText": "' . self::$LITERALES[$lang]["iframeEsignSignButtonText"] . '",
          "fitYourFace": "' . self::$LITERALES[$lang]["fitYourFace"] . '",
          "restartingErrorText": "' . self::$LITERALES[$lang]["restartingErrorText"] . '",
          "center_face": "' . self::$LITERALES[$lang]["center_face"] . '",
          "not_move": "' . self::$LITERALES[$lang]["not_move"] . '",
          "message_middle_center": "' . self::$LITERALES[$lang]["message_middle_center"] . '",
          "message_finish_challenge": "' . self::$LITERALES[$lang]["message_finish_challenge"] . '",
          "tooCloseText": "' . self::$LITERALES[$lang]["tooCloseText"] . '"
        },
        "contextualData": {
            "id": "' . $data["atfinity_case_id"] . '",
            "library_document_id": "' . $library_document_id . '"
        },
        "mobileQrRedirect": "' . $this->settings["mobile_redirect"] . '",
        "mobileQrParams": {
          "step": "4",
          "hash": "' . $data['hash'] . '",
          "userName": "' . $data["atfinity_first_name"] . '",
          "userEmail": "' . $data["atfinity_email"] . '"
        },
        "disableContinueOnDesktop": ' . $disableContinueOnDesktop . ',
        "esign": {
          "title": "BBVA NEW GEN CONTRACT",
          "signers": [{"name": "'. $userName.'", "email": "'.$email.'"}],
          "library_document_ids":["'. $library_document_id .'"]
        }
      }'
    ];

    

    if (isset($data['validationId'])) {
      $ret['validationId'] = $data['validationId'];
    }

    return $ret;
  }

  /*
   * Comprueba si la repuesta del servicio XpressID ha sido
   * exitosa y contiene un access_token y validation_id.
   * Retorna un array con información sobre el caso y si hay error o no
   */
  public function checkXpressIDResponse(string $curlResponse)
  {

    $resp = json_decode($curlResponse);

    $response = [];

    if (property_exists($resp, "access_token") && property_exists($resp, "validation_id")) {
      $response['error'] = ($resp->access_token && $resp->validation_id) ? false : true;
      $response['info'] = $resp;
      $response['access_token'] = ($resp->access_token) ? $resp->access_token : "";
      $response['validation_id'] = ($resp->validation_id) ? $resp->validation_id : "";
    } else {
      $response['error'] = true;
      $response['info'] = $resp;
      $response['access_token'] = "";
      $response['validation_id'] = "";
    }

    return $response;
  }


}