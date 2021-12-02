
<?php

use Source\Controller\SiteController;
use Source\Controller\MailerController;

$app->post('/send-form-contact', MailerController::class . ':sendFormContact');

$app->get('/', SiteController::class . ':index');

$app->get('/view-pdf/{hash}', SiteController::class . ':viewPdf');

$app->get('/curso/{id}', SiteController::class . ':showCurso');

#$app->get('/sampler', SiteController::class . ':sampler');
//FOCO
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

$app->get('/{slug}', SiteController::class . ':showPage');
