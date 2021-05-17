<?php

ob_start();

error_reporting(1);

session_start();

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
