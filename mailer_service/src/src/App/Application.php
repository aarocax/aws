<?php

namespace METRIC\App;

use METRIC\App\Http\Router;
use METRIC\App\Http\Request;
use METRIC\App\Http\Response;
use METRIC\App\Logger\Logger;
use METRIC\App\Email\Emailer;

class Application
{
	private $request_uri;
	private $request_method;
	private $content_type;
	private $logger;
	private $emailer;

	private $response;

	public function __construct(string $request_uri, string $request_method, string $content_type)
	{
		$this->request_uri = $request_uri;
		$this->request_method = $request_method;
		$this->content_type = $content_type;

		$this->logger = new Logger;
		$this->emailer = new Emailer();

		Router::get('/live', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {
			$res->status(400)->toText("mailer live...");
			exit;
		});

		// securizar con un header token, para saber que la petición viene de una fuente segura
		Router::post('/send', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$emailParams = $req->getBody();

			$this->logger->info($emailParams);
		
			$this->emailer->sendMail(
				$emailParams['to'],
				$emailParams['subject'],
				$emailParams['body'],
				explode(',', $emailParams['cc']),
				(array_key_exists('username', $emailParams)) ? $emailParams['username'] : null,
				(array_key_exists('email_reply_to', $emailParams)) ? $emailParams['email_reply_to'] : null,
			);
			
			$res->status(200);
			exit;
		});

		Router::post('/send/retargeting', $this->request_uri, $this->request_method, $this->content_type, function (Request $req, Response $res) {

			$emailParams = $req->getBody();

			$this->logger->info('/send/retargeting');
		
			$sent = $this->emailer->sendMail(
				$emailParams['to'],
				$emailParams['subject'],
				$emailParams['body'],
				explode(',', $emailParams['cc']),
				(array_key_exists('username', $emailParams)) ? $emailParams['username'] : null,
				(array_key_exists('email_reply_to', $emailParams)) ? $emailParams['email_reply_to'] : null,
			);

			$this->response = [];
			
			if ($sent) {
				return true;
			} else {
				return false;
			}

		});




		
	}
}
