<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\User;
use Source\Model\ArticleCategory;

class ArticlesCategoriesController extends Controller
{
	
	public function index()
	{		
		$pg = $this->pagination('ArticleCategory','/admin/artigos-categorias');
		$page = new PageAdmin();
		$page->setTpl("articles-categories", array(
			"articles" => $pg['data'],
			"search" => $pg['search'],
			"pages"=> $pg['pages']
		));exit;		
	}

	public function create()
	{
		$page = new PageAdmin();
		$categories = (new ArticleCategory())->listAll();
		$page->setTpl("articles-categories-create", [
			'msgError' => ArticleCategory::getError(),
		]);exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$category = new ArticleCategory();
		$_POST = $request->getParsedBody();
		$data = filter_var_array($_POST, FILTER_SANITIZE_STRING);
		$_SESSION['recoversPost'] = $_POST;

		if (in_array("", $data)) {
			ArticleCategory::setError('Preencha todos os campos.');
			header("Location: /admin/artigos-categorias/create");
			exit;
		}

		$category->setData($data);
		$category->save();
		unset($_SESSION['recoversPost']);
		header("Location:/admin/artigos-categorias");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$category = new ArticleCategory();
		$category->get((int) $args['id']);
		$category->delete();
		header("Location: /admin/artigos-categorias");
		exit;		
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$category = new ArticleCategory();
		$category->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("articles-categories-update", [
			"category" => $category->getValues(),
			'msgError' => ArticleCategory::getError()
		]);exit;
	}

	public function update(Request $request, Response $response, array $args)
	{
		$category = new ArticleCategory();
		$category->get((int) $args['id']);
		$category->setData($request->getParsedBody());
		$category->save();
		header("Location: /admin/artigos-categorias");
		exit;
	}

}//End Class