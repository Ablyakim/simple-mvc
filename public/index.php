<?php
use Symfony\Component\HttpFoundation\Request;
use App\App;

require '../etc/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$request = Request::createFromGlobals();
$app = new App();

$response = $app->processRequest($request);
$response->send();
