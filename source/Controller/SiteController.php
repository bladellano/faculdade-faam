<?php

namespace Source\Controller;

use Carbon\Carbon;
use Source\Page;
use Source\Model\Photo;
use Source\Model\Banner;
use Source\Model\Evento;
use Source\Model\Article;
use Source\Model\PageSite;
use Source\Model\PhotoAlbum;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Source\Model\Curso;
use Source\Model\Ouvidoria;
use Source\Model\Parceiros;
use Source\Model\Vestibular;

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
    private $vestibular = NULL;

    public function __construct()
    {

        // Ouvidoria
        $ouvTipos = (new Ouvidoria())->listTipos();
        $ouvUsuarios = \Source\Model\Ouvidoria::listUsuarios();
        $ouvSetores = \Source\Model\Ouvidoria::listSetores();

        $this->page = new Page([], self::VIEW_SITE,[
            'ouvTipos'=>$ouvTipos,
            'ouvUsuarios'=>$ouvUsuarios,
            'ouvSetores'=>$ouvSetores,
        ]);

        $this->evento = new Evento();
        $this->banner = new Banner();
        $this->article = new Article();
        $this->album = new PhotoAlbum();
        $this->photo = new Photo();
        $this->curso = new Curso();
        $this->vestibular = new Vestibular();
        /* Faz com que não seja verificado usuário com sessão */
    }

    public function viewPdf(Request $request, Response $response, array $args)
    {
        $file = $args["hash"];
        $file = base64_decode($file);
        print("<embed src='../" . $file . "' width=\"98%\" height=\"900\" type='application/pdf'>");
        die;
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

    public function showVestibular(Request $request, Response $response, array $args)
    {

        $allVestibulares = $this->vestibular->listAll();

        foreach ($allVestibulares as &$vestibular) {

            $vestibular['tempo'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($vestibular['created_at'])))->locale('pt_BR')->diffForHumans();
        }

        $this->vestibular->getWithSlug($args["slug"]);
        $data =  $this->vestibular->getValues();
        $data['tempo'] =  Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($data['created_at'])))->locale('pt_BR')->diffForHumans();
        $this->page->setTpl("vestibular", [
            "vestibular" => $data,
            "vestibulares" => $allVestibulares
        ]);
        exit;
    }

    public function vestibulares()
    {

        $pg = $this->pagination('Vestibular', '/vestibulares', 3);

        $this->page->setTpl("vestibulares", array(
            "vestibulares" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    private function createUpdateFiquePorDentro()
    {
        $fiquePorDentro = current(FiquePorDentroController::getFiquePorDentro());

        if (empty($fiquePorDentro['link_externo'])) {
            $link = '/' . $fiquePorDentro['slug'];
            $target = "";
        } else {
            $link = $fiquePorDentro['link_externo'];
            $target = "_blank";
        }

        $html = '<h2>DESTAQUE </h2>';
        // $html .=    '<a href="' . $link . '" target="' . $target . '">
        //             <img src="../../' . $fiquePorDentro["image_thumb"] . '" class="img-fluid" alt="' . $fiquePorDentro["title"] . '">
        //         </a>';

         $html .=    '<img src="../../' . $fiquePorDentro["image_thumb"] . '" class="img-fluid" alt="' . $fiquePorDentro["title"] . '">';

        $arquivo = getcwd() . DS . "views" . DS . "site" . DS . "fique-por-dentro.html";

        file_put_contents($arquivo, $html);
    }

    private function createUpdateMenuVestibular()
    {
        $vestibulares = $this->vestibular->listAll(['ativo' => 1], 'id, nome, slug, periodo, edital, faca_sua_inscricao, forma_de_ingresso, tipo');
        $vestibulares = current((array) $vestibulares);

        $html = "";

        $slug = "";
        $nome = "";
        $periodo = "";

        foreach ((array) $vestibulares as $key => $value) {

            if ($key == 'slug') $slug = $value;
            if ($key == 'nome') $nome = $value;
            if ($key == 'periodo') $periodo = $value;

            // if ($key == 'faca_sua_inscricao')
            //     $html .= '<a class="dropdown-item" target="_blank" href="' . $value . '">Faça sua inscrição</a>';

            // if ($key == 'edital')
            //     $html .= '<a class="dropdown-item" target="_blank" href="' . $value . '">Edital</a>';

            // if ($key == 'forma_de_ingresso')
            //     $html .= '<a class="dropdown-item" href="' . $value . '">Forma de ingresso</a>';
        }

        // $html .= "<a class='dropdown-item' href='/vestibulares/{$slug}'>Vestibular {$nome} {$periodo}</a>";

        
        $html .= '<a class="dropdown-item" href="/vestibulares">Editais</a>';
        $html .= '<a class="dropdown-item" href="/formas-de-ingresso">Forma de ingresso</a>';


        $arquivo = getcwd() . DS . "views" . DS . "site" . DS . "menu-vestibular.html";

        file_put_contents($arquivo, $html);
    }

    private function createUpdateMenuPosGraduacao()
    {
        $cursos = $this->curso->listAllNamesCursosPosGraduacao();
        $html = "";

        foreach ($cursos as $curso)
            $html .= '<a class="dropdown-item" href="/curso/' . $curso["id"] . '">' . $curso["nome"] . '</a>';
        $arquivo = getcwd() . DS . "views" . DS . "site" . DS . "menu-pos-graduacao.html";

        file_put_contents($arquivo, $html);
    }
    private function createUpdateSobreFaamFrente()
    {
        $page = new \Source\Model\PageSite();
        $page->getWithSlug("quem-somos");
        $return = $page->getValues();

        $arquivo = getcwd() . DS . "views" . DS . "site" . DS . "sobre-faam-frente.html";

        file_put_contents($arquivo, $return["description"]);
    }

    public function index()
    {
        #Atualiza no menu os cursos existentes
        $this->createUpdateMenu();
        $this->createUpdateMenuPosGraduacao();
        $this->createUpdateSobreFaamFrente();
        $this->createUpdateMenuVestibular();
        $this->createUpdateFiquePorDentro();

        $articles = (new Article())->listAll("LIMIT 4");
        $banners = (new Banner())->listAll("LIMIT 4");
        $eventos = (new Evento())->listAll("LIMIT 4");
        $cursos = (new Curso())->listAll("LIMIT 10");
        $parceiros = (new Parceiros())->listAll();

        foreach ($eventos as &$evento) {
            $date = new \DateTime($evento["event_day"]);
            $evento['mes'] = mb_strtoupper(strftime('%b', strtotime($evento["event_day"])));
            $evento['dia'] = $date->format('d');
        }

        foreach ($articles as &$article) {
            $date = new \DateTime($article["created_at"]);
            $article['mes'] = mb_strtoupper(strftime('%b', strtotime($article["created_at"])));
            $article['dia'] = $date->format('d');
        }

        $this->page->setTpl("home", [
            'articles' => $articles,
            'banners' => $banners,
            'eventos' => $eventos,
            'cursos' => $cursos,
            'parceiros' => $parceiros,
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
