<?php

ob_start();

error_reporting(1);

session_start();

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

date_default_timezone_set('America/Sao_Paulo');

require_once "vendor/autoload.php";

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
];

$app = new \Slim\App($config);

require_once("routes/admin.php");
require_once("routes/site.php");
 
$app->run();
