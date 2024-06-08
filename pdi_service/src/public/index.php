<?php

use METRIC\App\Application;

require dirname(__DIR__).'/src/bootstrap.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$content_type = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : "" ;
$request_port = $_SERVER['SERVER_PORT'];

$app = new Application($request_uri, $request_method, $content_type, $request_port);