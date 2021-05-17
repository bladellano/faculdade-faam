<?php

use Source\Controller\AdminController;
use Source\Controller\PagesController;
use Source\Controller\UsersController;
use Source\Controller\AlbumsController;
use Source\Controller\BannersController;
use Source\Controller\EventosController;
use Source\Controller\ArticlesController;
use Source\Controller\ArticlesCategoriesController;

// error_reporting(0);

// TESTE
$app->get('/admin/delete-image',AdminController::class . ':deleteImage');
$app->post('/admin/send-image',AdminController::class . ':sendImage');
$app->get('/admin/list-images',AdminController::class . ':listImages');

/**
 * LOGIN
 */
$app->group('/admin', function () use ($app) {
    $app->get('', AdminController::class . ':index');
    $app->get('/login', AdminController::class . ':screen');//Layout
    $app->post('/login', AdminController::class . ':logging');
    $app->get('/logout', AdminController::class . ':logout');
});

/**
 * ADMIN EVENTOS
 */
$app->group('/admin/eventos', function () use ($app) {
    $app->get('', EventosController::class . ':index');
    $app->post('/change-order', EventosController::class . ':changeOrder');
    $app->post('/change-status', EventosController::class . ':changeStatus');
    $app->get('/create', EventosController::class . ':create');
    $app->post('/store', EventosController::class . ':store');
    $app->get('/{id}', EventosController::class . ':edit');
    $app->put('/{id}', EventosController::class . ':update');
    $app->get('/{id}/delete', EventosController::class . ':destroy');
}); 

/**
 * ADMIN FOTOS
 */

$app->get('/admin/photos/{id_photo}/{id_album}/delete', AlbumsController::class . ':destroyPhoto');
$app->get('/admin/show-album/{id}', AlbumsController::class . ':get');

$app->group('/admin/albums', function () use ($app) {
    $app->get('', AlbumsController::class . ':index');
    $app->get('/change-cover/{id_photo}/{id_album}', AlbumsController::class . ':changeCover');
    $app->post('/create-name', AlbumsController::class . ':createNameAlbum');
    $app->get('/create', AlbumsController::class . ':create');
    $app->post('/store', AlbumsController::class . ':store');
    $app->get('/{id}', AlbumsController::class . ':edit');
    $app->put('/{id}', AlbumsController::class . ':update');
    $app->get('/{id}/delete', AlbumsController::class . ':destroy');
});


/**
 * ADMIN BANNERS
 */

$app->group('/admin/banners', function () use ($app) {
    $app->get('', BannersController::class . ':index');
    $app->get('/create', BannersController::class . ':create');
    $app->post('/store', BannersController::class . ':store');
    $app->get('/{id}', BannersController::class . ':edit');
    $app->put('/{id}', BannersController::class . ':update');
    $app->get('/{id}/delete', BannersController::class . ':destroy');
});

/**
 * ADMIN PÁGINAS
 */

$app->group('/admin/paginas', function () use ($app) {
    $app->get('', PagesController::class . ':index');
    $app->get('/create', PagesController::class . ':create');
    $app->post('/store', PagesController::class . ':store');
    $app->get('/{id}', PagesController::class . ':edit');
    $app->put('/{id}', PagesController::class . ':update');
    $app->get('/{id}/delete', PagesController::class . ':destroy');
});

/**
 * ADMIN ARTIGOS CATEGORIAS
 */

$app->group('/admin/artigos-categorias', function () use ($app) {
    $app->get('', ArticlesCategoriesController::class . ':index');
    $app->get('/create', ArticlesCategoriesController::class . ':create');
    $app->post('/store', ArticlesCategoriesController::class . ':store');
    $app->get('/{id}', ArticlesCategoriesController::class . ':edit');
    $app->put('/{id}', ArticlesCategoriesController::class . ':update');
    $app->get('/{id}/delete', ArticlesCategoriesController::class . ':destroy');
});

/**
 * ADMIN ARTIGOS
 */

$app->group('/admin/artigos', function () use ($app) {
    $app->get('', ArticlesController::class . ':index');
    $app->get('/create', ArticlesController::class . ':create');
    $app->post('/store', ArticlesController::class . ':store');
    $app->get('/{id}', ArticlesController::class . ':edit');
    $app->put('/{id}', ArticlesController::class . ':update');
    $app->get('/{id}/delete', ArticlesController::class . ':destroy');
});

/**
 * ADMIN USUÁRIOS
 */
$app->group('/admin/users', function () use ($app) {
    $app->get('', UsersController::class . ':index');
    $app->get('/create', UsersController::class . ':create');
    $app->get('/{id}', UsersController::class . ':edit');
    $app->post('/store', UsersController::class . ':store');
    $app->put('/{id}', UsersController::class . ':update');
    $app->get('/{id}/delete', UsersController::class . ':destroy');
    $app->put('/{id}/password', UsersController::class . ':updatePassword');
    $app->get('/{id}/password', UsersController::class . ':changePassword');
});