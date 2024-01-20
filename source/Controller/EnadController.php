<?php

namespace Source\Controller;

use Source\PageAdmin;

use Source\Model\Enad;
use Ausi\SlugGenerator\SlugGenerator;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class EnadController extends Controller
{

    private $enad;

    public function __construct()
    {
        $this->enad = new Enad();
    }

    public function index()
    {
        $pg = $this->pagination('Enad', '/admin/enads');

        $page = new PageAdmin();

        $page->setTpl("enads", [
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

		$this->enad->setData($data);
		$this->enad->save();
        
		header("Location:/enade?successfully=1");
		exit;

    }

}
