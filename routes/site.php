
<?php

use Source\Controller\SiteController;
use Source\Controller\MailerController;

$app->post('/send-form-contact', MailerController::class . ':sendFormContact');

$app->get('/', SiteController::class . ':index');

$app->get('/sampler', SiteController::class . ':sampler');
$app->get('/albuns', SiteController::class . ':albums');
$app->get('/album/{id}', SiteController::class . ':showPhotos');

$app->get('/eventos', SiteController::class . ':events'); //CONTEÚDO DINÂMICO.
$app->get('/evento/{slug}', SiteController::class . ':showEvent'); //CONTEÚDO DINÂMICO.

$app->get('/noticias', SiteController::class . ':articles'); //CONTEÚDO DINÂMICO.
$app->get('/noticia/{slug}', SiteController::class . ':showArticle'); //CONTEÚDO DINÂMICO.

$app->get('/{slug}', SiteController::class . ':showPage');
