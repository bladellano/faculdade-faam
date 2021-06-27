<?php

namespace Source\Controller;

use Source\Page;
use Faker\Factory;
use Source\Model\Photo;
use Source\Model\Banner;
use Source\Model\Evento;
use Source\Model\Article;
use Source\Model\PageSite;
use Source\Model\PhotoAlbum;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Source\Model\Curso;

class SiteController extends Controller
{
    private const VIEW_SITE = "/views/site/";

    private $page = NULL;
    private $evento = NULL;
    private $banner = NULL;
    private $article = NULL;
    private $album = NULL;
    private $photo = NULL;
    private $curso = NULL;

    public function __construct()
    {

        $this->page = new Page([], self::VIEW_SITE);
        $this->evento = new Evento();
        $this->banner = new Banner();
        $this->article = new Article();
        $this->album = new PhotoAlbum();
        $this->photo = new Photo();
        $this->curso = new Curso();
        /* Faz com que não seja verificado usuário com sessão */
    }

    public function showCurso(Request $request, Response $response, array $args)
    {
        $curso = $this->curso->get($args["id"]);
        $curso = $this->curso->getValues();

        #Obtêm todos os anexos
        $anexos = $this->curso->getAnexosCurso($args["id"]);

        $this->page->setTpl("curso", [
            'curso' => $curso,
            'anexos' => $anexos
        ]);
        exit;
    }

    public function showPhotos(Request $request, Response $response, array $args)
    {
        $data = $this->album->getPhotos((int) $args['id']);
        $this->page->setTpl("photos", [
            'photos' => $data
        ]);
        exit;
    }
    public function albums()
    {
        $albums =  $this->album->listAll();

        foreach ($albums as &$album) {
            $this->photo->get($album["id_photos_cover"]);
            $album["cover"] = $this->photo->getimage_thumb();
        }

        $this->page->setTpl("albums", [
            'albums' => $albums
        ]);
        exit;
    }

    /**
     * Gera a string do menu dos cursos dinamicamente
     * @return void
     */
    private function createUpdateMenu()
    {
        $cursos = $this->curso->listAllNamesCursos();
        $html = "";

        foreach ($cursos as $curso)
            $html .= '<a class="dropdown-item" href="/curso/' . $curso["id"] . '">' . $curso["nome"] . '</a>';
        $arquivo = getcwd() . DS . "views" . DS . "site" . DS . "menu.html";

        file_put_contents($arquivo, $html);
    }

    public function index()
    {
        #Atualiza no menu os cursos existentes
        $this->createUpdateMenu();

        $articles = (new Article())->listAll("Limit 3");
        $banners = (new Banner())->listAll();

        foreach ((array) $articles as &$article) {
            $date = new \DateTime('2021-05-17 20:47:01');
            $article['mes'] = $date->format('M');
            $article['dia'] = $date->format('d');
        }

        $this->page->setTpl("home", [
            'articles' => $articles,
            'banners' => $banners
        ]);
        exit;
    }

    /**
     * Exibe páginas avulsas
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return void
     */
    public function showPage(Request $request, Response $response, array $args)
    {
        $page_site = new PageSite();
        $page_site->getWithSlug($args["slug"]);
        $data = $page_site->getValues();
        $name_page = 'page';
        if (!count($data))
            $name_page = 'not-found';
        $this->page->setTpl($name_page, ['data' => $data]);
        exit;
    }

    public function showArticle(Request $request, Response $response, array $args)
    {

        $all_articles = $this->article->listAll();
        $this->article->getWithSlug($args["slug"]);
        $data =  $this->article->getValues();
        $this->page->setTpl("article", [
            "article" => $data,
            "articles" => $all_articles
        ]);
        exit;
    }

    public function articles()
    {

        $pg = $this->pagination('Article', '/noticias');

        $this->page->setTpl("articles", array(
            "articles" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    public function events()
    {

        $pg = $this->pagination('Evento', '/eventos');
        $this->page->setTpl("eventos", array(
            "eventos" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    public function showEvent(Request $request, Response $response, array $args)
    {

        $all_eventos = $this->evento->listAll();
        $this->evento->getWithSlug($args["slug"]);
        $data =  $this->evento->getValues();
        $this->page->setTpl("evento", [
            "evento" => $data,
            "eventos" => $all_eventos
        ]);
        exit;
    }
    public function showBanner(Request $request, Response $response, array $args)
    {

        $allBanners = $this->banner->listAll();
        $this->banner->getWithSlug($args["slug"]);
        $data =  $this->banner->getValues();

        $this->page->setTpl("banner", [
            "banner" => $data,
            "banners" => $allBanners
        ]);
        exit;
    }
}
