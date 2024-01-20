<?php

namespace Source\Controller;

use Source\PageAdmin;

use Source\Model\Egresso;
use Ausi\SlugGenerator\SlugGenerator;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class EgressosController extends Controller
{

    private $egresso;

    public function __construct()
    {
        $this->egresso = new Egresso();
    }

    public function index()
    {
        $pg = $this->pagination('Egresso', '/admin/egressos');

        $page = new PageAdmin();

        $page->setTpl("egressos", [
            "data" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ]);

        exit;
    }

    public function store(Request $request, Response $response, array $args)
    {

		$data = $request->getParsedBody();

		$data['slug'] = (new SlugGenerator)->generate($data['name']);

		$this->egresso->setData($data);
		$this->egresso->save();
        
		header("Location:/egresso?successfully=1");
		exit;

    }

}
