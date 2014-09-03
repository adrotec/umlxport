<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$vendorDir = isset($_GET['new']) ? 'vendor_new' : 'vendor';

require_once __DIR__."/../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
 
$request = Request::createFromGlobals();

if(!isset($assetsDir)){
    $assetsDir = 'assets';
}
$appName = 'UmlXport';
$appDescription = 'Generate Doctrine Mappings from your UML class diagrams.';

require __DIR__.'/main.php';

ob_start();
require __DIR__.'/view.php';
$content = ob_get_clean();

ob_start();
require __DIR__.'/layout.php';
$content = ob_get_clean();

 
$response = new Response($content);
 
$response->send();