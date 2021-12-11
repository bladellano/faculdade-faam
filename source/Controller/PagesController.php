<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Page;
use Ausi\SlugGenerator\SlugGenerator;

class PagesController extends Controller
{

	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "fique-por-dentro";

	public function index()
	{
		$pg = $this->pagination('Page', '/admin/paginas');
		$page = new PageAdmin();
		$page->setTpl("pages", array(
			"p" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages']
		));
		exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$page->setTpl("pages-create", [
			'msgError' => Page::getError(),
			'scripts' => ['https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$generator = new SlugGenerator;

		$_POST = $request->getParsedBody();

		$_POST['slug'] = $generator->generate($_POST['title']);

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

		$disabled = [];

		if ($args['id'] == 49) { //Código da página 'FIQUE POR DENTRO'

			$disabled = [
				'title' => 1,
				'slug' => 1,
				'author' => 1,
			];
		}

		$p = new Page();
		$p->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("pages-update", [
			'disabled' => $disabled,
			"p" => $p->getValues(),
			'msgError' => Page::getError(),
			'msgSuccess' => Page::getSuccess(),
			'scripts' => ['https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function update(Request $request, Response $response, array $args)
	{
		$generator = new SlugGenerator;

		if ($args['id'] != 49)
			$_POST['slug'] = $generator->generate($_POST['title']);

		/* Valida se $_FILES existem com imagem */
		if ($args['id'] == 49) {

			if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

				$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

				if (is_array($images) && !count($images)) {
					Page::setError(self::$msgError);
					header("Location: /admin/paginas/{$args['id']}");
					exit;
				}

				$_POST['image'] = $images['image'];
				$_POST['image_thumb'] = $images['image_thumb'];
			}
		}
		/* End */
		unset($_POST['_METHOD']);

		$p = new Page();
		$p->get((int) $args['id']);
		$p->setData($_POST);
		$p->save();

		Page::setSuccess('Atualizado com sucesso!');

		header("Location: /admin/paginas/{$args['id']}");
		exit;
	}
}//End Class