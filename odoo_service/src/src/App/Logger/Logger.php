<?php 

namespace METRIC\App\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use METRIC\App\Config\AppConfig;

class Logger
{
  
  private $logger;

  public function __construct()
  {
    $this->logger = new MonologLogger('send.data');
    $formatter = new LineFormatter(null, null, false, true);
    $defaultHandler = new StreamHandler(AppConfig::getInstance()->getValue("LOG_OUT"), Level::Debug);
    $defaultHandler->setFormatter($formatter);
    $this->logger->pushHandler($defaultHandler);
  }

  public function error($data): void
  {
    $this->logger->error(var_export($data, true));
  }

  public function info($data): void
  {
    $this->logger->info(var_export($data, true));
  }
}