
<?php

use Source\Controller\SiteController;
use Source\Controller\MailerController;
use Source\Controller\EgressosController;
use Source\Controller\EnadController;
use Source\Controller\OuvidoriaController;

$app->post('/send-form-contact', MailerController::class . ':sendFormContact');

$app->post('/ouvidoria/store', OuvidoriaController::class . ':store');
$app->get('/', SiteController::class . ':index');

$app->get('/view-pdf/{hash}', SiteController::class . ':viewPdf');

$app->get('/curso/{id}', SiteController::class . ':showCurso');

$app->get('/vestibulares', SiteController::class . ':vestibulares');
$app->get('/vestibulares/{slug}', SiteController::class . ':showVestibular');

$app->get('/albuns', SiteController::class . ':albums');
$app->get('/album/{id}', SiteController::class . ':showPhotos');

$app->get('/banners', SiteController::class . ':banners');
$app->get('/banners/{slug}', SiteController::class . ':showBanner');

$app->get('/eventos', SiteController::class . ':events');
$app->get('/evento/{slug}', SiteController::class . ':showEvent');

$app->get('/noticias', SiteController::class . ':articles');
$app->get('/noticia/{slug}', SiteController::class . ':showArticle');

$app->get('/egresso', SiteController::class . ':egresso');
$app->post('/egresso', EgressosController::class . ':store');

$app->get('/enade', SiteController::class . ':enad');
$app->post('/enad', EnadController::class . ':store');

$app->get('/{slug}', SiteController::class . ':showPage');
