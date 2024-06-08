<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use METRIC\App\Config\AppConfig;

// cargar las variables del archivo de configuración en una clase para que sea accesible desde cualquier punto de la aplicación
foreach ($_ENV as $key => $value) {
	if (!is_array($value)) {
		AppConfig::getInstance()->setValue($key, $value);
	}
}


