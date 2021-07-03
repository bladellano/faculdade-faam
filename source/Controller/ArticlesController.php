<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Article;
use Source\Model\ArticleCategory;

class ArticlesController extends Controller
{
	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "articles";

	public function index()
	{
		/* Chama paginação do Controller */
		$pg = $this->pagination('Article', '/admin/artigos');
		$page = new PageAdmin();
		$page->setTpl("articles", array(
			"articles" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages']
		));
		exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$categories = (new ArticleCategory())->listAll();
		$page->setTpl("articles-create", [
			'msgError' => Article::getError(),
			'categories' => $categories,
			'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRING);

		$_SESSION['recoversPost'] = $request->getParsedBody();

		if (in_array("", $data)) {
			Article::setError('Preencha todos os campos.');
			header("Location: /admin/artigos/create");
			exit;
		}

		/* Valida se $_FILES existem com imagem */
		if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

			$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

			if (is_array($images) && !count($images)) {
				Article::setError(self::$msgError);
				header("Location: /admin/artigos/create");
				exit;
			}

			$data['image'] = $images['image'];
			$data['image_thumb'] = $images['image_thumb'];
		} /* End */

		$article = new Article();
		$article->setData($data);
		$article->save();

		unset($_SESSION['recoversPost']);
		header("Location:/admin/artigos");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$article = new Article();
		$article->get((int) $args['id']);

		if (file_exists($article->getimage())) {
			unlink($article->getimage());
			unlink($article->getimage_thumb());
		}

		$article->delete();
		header("Location: /admin/artigos");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$article = new Article();

		$categories = (new ArticleCategory())->listAll();

		$article->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("articles-update", [
			"article" => $article->getValues(),
			'msgError' => Article::getError(),
			'categories' => $categories,
			'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function update(Request $request, Response $response, array $args)
	{

		$article = new Article();

		unset($_POST['_METHOD']);

		$article->get((int) $args['id']);

		/* Verifica se existe arquivo para ser enviado */
		if ($_FILES['image']['error'] == 0) {

			/*Primeiro apaga imagens anteriores*/
			if (file_exists($article->getimage())) {
				unlink($article->getimage());
				unlink($article->getimage_thumb());
			}

			$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

			if (!count((array) $images)) {
				Article::setError(self::$msgError);
				header("Location: /admin/artigos/" . $args['id']);
				exit;
			}

			$_POST['image'] = $images['image'];
			$_POST['image_thumb'] = $images['image_thumb'];
		}

		if (!isset($_POST['spotlight']) || !isset($_POST['show_author'])) {
			$_POST['spotlight'] = $_POST['spotlight'];
			$_POST['show_author'] = $_POST['show_author'];
		}

		$article->setData($_POST);
		$article->save();

		header("Location:/admin/artigos/" . $args['id']);
		exit;
	}
}//End Class