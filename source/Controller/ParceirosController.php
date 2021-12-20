<?php

namespace Source\Controller;

use Source\PageAdmin;

use Source\Model\Parceiros;
use Ausi\SlugGenerator\SlugGenerator;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class ParceirosController extends Controller
{
	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "parceiros";

	public function index()
	{
		/* Chama paginação do Controller */
		$pg = $this->pagination('Parceiros', '/admin/parceiros');
		$page = new PageAdmin();
		$page->setTpl("parceiros", array(
			"parceiros" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages'],
			'msgError' => Parceiros::getError()
		));
		exit;
	}

	public function inbox()
	{
		$parceiros = $this->pagination('Parceiros', '/admin/parceiros');

		$parceiros = array_map(function ($item) {
			$item['status'] = ($item['status'] == 1) ? "SIM" : "NÃO";
			return $item;
		}, $parceiros['data']);

		print(json_encode(['data' => $parceiros]));
		exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		
		$_SESSION['recoversPost'] = $request->getParsedBody();

		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRING);

		if (in_array("", $data)) {
			Parceiros::setError('Preencha todos os campos.');
			header("Location: /admin/parceiros");
			exit;
		}

		if ($_FILES['image']['error'] == 4) {
			Parceiros::setError('Preencha todos os campos. Selecione uma imagem.');
			header("Location: /admin/parceiros");
			exit;
		}

		$generator = new SlugGenerator;

		$data['slug'] = $generator->generate($data['name']);

		/* Valida se $_FILES existem com imagem */
		if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

			$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

			if (is_array($images) && !count($images)) {
				Parceiros::setError(self::$msgError);
				header("Location: /admin/parceiros");
				exit;
			}

			$data['image'] = $images['image'];
			$data['image_thumb'] = $images['image_thumb'];
		}
		/* End */

		$vestibular = new Parceiros();
		$vestibular->setData($data);
		$vestibular->save();

		unset($_SESSION['recoversPost']);
		header("Location:/admin/parceiros");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$parceiro = new Parceiros();
		$parceiro->get((int) $args['id']);

		if (file_exists($parceiro->getimage())) {
			unlink($parceiro->getimage());
			unlink($parceiro->getimage_thumb());
		}

		$parceiro->delete();
		header("Location: /admin/parceiros");
		exit;
	}

}//End Class