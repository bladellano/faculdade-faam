<?php

use Source\Controller\AdminController;
use Source\Controller\PagesController;
use Source\Controller\UsersController;
use Source\Controller\AlbumsController;
use Source\Controller\CursosController;
use Source\Controller\BannersController;
use Source\Controller\EventosController;
use Source\Controller\ArticlesController;
use Source\Controller\OuvidoriaController;
use Source\Controller\ParceirosController;
use Source\Controller\VestibularesController;
use Source\Controller\FiquePorDentroController;
use Source\Controller\ArticlesCategoriesController;
use Source\Controller\EgressosController;
use Source\Controller\EnadController;

/**
 * IMAGES/PDF AVULSOS
 */
$app->get('/admin/delete-image', AdminController::class . ':deleteImage');
$app->post('/admin/send-image', AdminController::class . ':sendImage');
$app->get('/admin/list-images', AdminController::class . ':listImages');

/**
 * LOGIN
 */
$app->group('/admin', function () use ($app) {
    $app->get('', AdminController::class . ':index');
    $app->get('/login', AdminController::class . ':screen'); //Layout
    $app->post('/login', AdminController::class . ':logging');
    $app->get('/logout', AdminController::class . ':logout');
});


/**
 * ADMIN CURSOS
 */

$app->get('/admin/pos-graduacao', CursosController::class . ':showListPosGraduacao');

$app->group('/admin/cursos', function () use ($app) {

    $app->get('', CursosController::class . ':index');
    $app->post('/delete-doc', CursosController::class . ':deleteDoc');
    $app->get('/create', CursosController::class . ':create');
    $app->post('/store', CursosController::class . ':store');
    $app->get('/{id}', CursosController::class . ':edit');
    $app->put('/{id}/update', CursosController::class . ':update');
    $app->get('/{id}/delete', CursosController::class . ':destroy');
});

/**
 * ADMIN OUVIDORIA
 */
$app->group('/admin/ouvidoria', function () use ($app) {
    $app->get('', OuvidoriaController::class . ':index');
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
 * ADMIN VESTIBULARES
 */
$app->group('/admin/vestibulares', function () use ($app) {
    $app->get('', VestibularesController::class . ':index');
    $app->get('/create', VestibularesController::class . ':create');
    $app->post('/store', VestibularesController::class . ':store');
    $app->post('/update-active-vestibular', VestibularesController::class . ':updateActiveVestibular');
    $app->get('/{id}', VestibularesController::class . ':edit');
    $app->put('/{id}', VestibularesController::class . ':update');
    $app->get('/{id}/delete', VestibularesController::class . ':destroy');
});

/**
 * ADMIN PARCEIROS
 */
$app->group('/admin/parceiros', function () use ($app) {
    $app->get('', ParceirosController::class . ':index');
    
    $app->get('/inbox', ParceirosController::class . ':inbox');

    $app->get('/create', ParceirosController::class . ':create');
    $app->post('/store', ParceirosController::class . ':store');
    // $app->post('/update-active-vestibular', ParceirosController::class . ':updateActiveVestibular');
    $app->get('/{id}', ParceirosController::class . ':edit');
    $app->put('/{id}', ParceirosController::class . ':update');
    $app->get('/{id}/delete', ParceirosController::class . ':destroy');
});

$app->group('/admin/fique-por-dentro', function () use ($app) {
    $app->get('', FiquePorDentroController::class . ':index');
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

/**
 * ADMIN EGRESSOS
 */
$app->group('/admin/egressos', function () use ($app) {
    $app->get('', EgressosController::class . ':index');
});

/**
 * ADMIN EGRESSOS
 */
$app->group('/admin/enads', function () use ($app) {
    $app->get('', EnadController::class . ':index');
});
