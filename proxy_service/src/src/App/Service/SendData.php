<?php 

namespace METRIC\App\Service;

use METRIC\App\Config\AppConfig;
use METRIC\App\Service\CurlClass;

class SendData
{


	public static function send(string $url, array $headers, array $query, string $method='POST') {


		CurlClass::prepare(AppConfig::getInstance()->getValue("INTEGRATION_SERVER_HOST") . $url, $headers, $query);
		if ($method === 'POST') {
			CurlClass::exec_post();
		} else {
			CurlClass::exec_get();
		}

		return CurlClass::get_response();
	}

	public static function sendContact(string $url, array $headers, array $query, string $method='POST') {


		CurlClass::prepare(AppConfig::getInstance()->getValue("CONTACT_SERVER_HOST") . $url, $headers, $query);
		if ($method === 'POST') {
			CurlClass::exec_post();
		} else {
			CurlClass::exec_get();
		}

		return CurlClass::get_response();
	}

	public static function redirectionOdoo(string $url, array $headers, array $query, string $method='POST') {


		CurlClass::prepare(AppConfig::getInstance()->getValue("ODOO_SERVICE") . $url, $headers, $query);
		if ($method === 'POST') {
			CurlClass::exec_post();
		} else {
			CurlClass::exec_get();
		}

		return CurlClass::get_response();
	}



}