<?php

use App\App;
use Symfony\Component\HttpFoundation\Request;

require '../etc/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

$request = Request::createFromGlobals();
$app = new App();

$response = $app->processRequest($request);
$response->send();
