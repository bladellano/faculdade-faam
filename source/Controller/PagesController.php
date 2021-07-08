<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Page;

class PagesController extends Controller
{
	public function index()
	{		
		$pg = $this->pagination('Page','/admin/paginas');
		$page = new PageAdmin();
		$page->setTpl("pages", array(
			"p" => $pg['data'],
			"search" => $pg['search'],
			"pages"=> $pg['pages']
		));exit;		
	}

	public function create()
	{
		$page = new PageAdmin();
		$page->setTpl("pages-create", [
			'msgError' => Page::getError(),
			'scripts' => ['https://cloud.tinymce.com/stable/tinymce.min.js','/views/admin/assets/js/form.js']
		]);exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$_POST = $request->getParsedBody();
		$p = new Page();
		$data = $_POST;
		$_SESSION['recoversPost'] = $_POST;
		if (in_array("", $data)) {
			Page::setError('Preencha todos os campos.');
			header("Location: /admin/paginas/create");
			exit;
		}
		$p->setData($data);
		$p->save();
		unset($_SESSION['recoversPost']);
		header("Location:/admin/paginas");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$p = new Page();
		$p->get((int) $args['id']);
		$p->delete();
		header("Location: /admin/paginas");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$p = new Page();
		$p->get((int)  $args['id']);
		$page = new PageAdmin();
		$page->setTpl("pages-update", [
			"p" => $p->getValues(),
			'msgError' => Page::getError(),
			'scripts' => ['https://cloud.tinymce.com/stable/tinymce.min.js','/views/admin/assets/js/form.js']
		]);exit;

	}

	public function update(Request $request, Response $response, array $args)
	{
		
		$p = new Page();
		$p->get((int) $args['id']);
		$p->setData($_POST);
		$p->save();
		header("Location: /admin/paginas");
		exit;
	}

}//End Class