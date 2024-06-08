<?php

namespace METRIC\App\Controller;

use METRIC\App\Logger\Logger;
use METRIC\App\Config\AppConfig;
use METRIC\App\Email\Emailer;


class BaseController {

    const TIMEOUT = 60;

    private static $LOG_LITERALES = array(
        'METRIC\App\Controller\BoiDasController' => array(
            'subject' => 'Boidas Service',
            'isAlive' => array(
                'subject' => 'isAlive call',
                'url' => 'boidas/alive'
            )
        ),

        'METRIC\App\Controller\EsignController' => array(
            'subject' => 'Esign Service',
            'send' => array(
                'subject' => 'send call',
                'url' => ''
            ),
            'sendData' => array(
                'subject' => 'sendData call',
                'url' => ''
            )
        )
    );

    protected $appConfig = null;
    protected $logger2;
    protected $classReflector = null;
    private $emailer;

    public function __construct()
    {
        // registrar clase hija
        $classChild = get_class($this);
        $this->classReflector = new \ReflectionClass($classChild);
        // clase de logs
        $this->logger2 = new Logger;
        $this->appConfig = AppConfig::getInstance();
        $this->emailer = new Emailer();

    }


    // cuando $methodNameComplete no está vacío, se llama con __METHOD__ o el nombre del método
    protected function logInfo($message, $methodNameComplete='') {
        $this->logWithMethod('info', $message, $methodNameComplete);
    }

    protected function logError($message, $sendEmail=false, $methodNameComplete='') {
        $this->logWithMethod('error', $message, $methodNameComplete);
        if ($sendEmail) {
            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }
            $text = sprintf("%s", $message);
            $emailResult = $this->emailer->sendMail(
                $this->appConfig->getValue("EMAIL_SERVICE_TO"),
                "[Onboarding] Error",
                "<p>For your attention,</p><p>Error, </p><p>" . $text  . "</p>",
                explode(',', $this->appConfig->getValue("EMAIL_SERVICE_CC"))
            );
        }
    }


    // buscar método mediante parámetro directo o con la traza de una excepción silenciosa y logar el mensaje
    protected function logWithMethod($level, $message, $methodNameComplete='')
    {
        try {
            // recoger método mediante la traza de una excepción silenciosa
            if ($methodNameComplete === '') {
                //$methodNameComplete = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
                $ex = new \Exception();
                $trace = $ex->getTrace();
                if ($trace[0]['function'] === 'logInfo' || $trace[0]['function'] === 'logError') {
                    $methodNameComplete = $trace[1]['function'];
                } else if ($trace[1]['function'] === 'logInfo' || $trace[1]['function'] === 'logError') {
                    $methodNameComplete = $trace[2]['function'];
                } else {
                    $methodNameComplete = $trace[1]['function'];
                }
            }

            // método con namespace, nos quedamos sólo con el nombre
            if (strpos($methodNameComplete, ':') !== false) {
                $methodName = substr($methodNameComplete, strrpos($methodNameComplete, ':') + 1);
            } else {
                $methodName = $methodNameComplete;
            }

            // comprobar que el método existe en la clase
            $methodObj = $this->classReflector->getMethod($methodName);

            // buscar literal de la clase y el método
            if (isset(self::$LOG_LITERALES[$methodObj->class]))
            {
                $subject = sprintf("%s: %s",
                    self::$LOG_LITERALES[$methodObj->class]['subject'],
                    (isset(self::$LOG_LITERALES[$methodObj->class][$methodObj->name])) ?
                        self::$LOG_LITERALES[$methodObj->class][$methodObj->name]['subject']
                        : $methodName
                );
            } else {
                $subject = sprintf("%s: %s", $methodObj->class, $methodObj->name);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }
            $text = sprintf("[%s] %s", $subject, $message);

            $this->logText($level, $text);
        } catch (\Exception $e) {
            //$this->logText('error', $e->getMessage());
            $this->logText($level, $message);
        }
    }

    // logar
    private function logText($level, $text) {
        switch($level) {
            case 'info':
                $this->logger2->info($text);
                break;
            case 'error':
                $this->logger2->error($text);
                break;
        }
    }
}