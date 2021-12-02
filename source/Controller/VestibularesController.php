<?php

namespace Source\Controller;

use Source\PageAdmin;

use Source\Model\Vestibular;
use Ausi\SlugGenerator\SlugGenerator;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Source\DB\Sql;

class VestibularesController extends Controller
{
	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "vestibulares";

	public function index()
	{
		/* Chama paginação do Controller */
		$pg = $this->pagination('Vestibular', '/admin/vestibulares');
		$page = new PageAdmin();
		$page->setTpl("vestibulares", array(
			"vestibulares" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages']
		));
		exit;
	}

	public function updateActiveVestibular()
	{

		$id = $_POST['id'];

		$sql = new Sql();

		$sql->query('UPDATE vestibulares r SET r.ativo = 0');

		$sql->update('vestibulares', ['id' => $id, 'ativo' => $_POST['ativar']]);

		print(json_encode(['success' => true, 'msg' => 'Ativado com sucesso!']));
		exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$page->setTpl("vestibulares-create", [
			'msgError' => Vestibular::getError(),
			'scripts' => [
				'https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js',
				'/views/admin/assets/js/form.js'
			]
		]);
		exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$_SESSION['recoversPost'] = $request->getParsedBody();

		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRING);

		if (in_array("", $data)) {
			Vestibular::setError('Preencha todos os campos.');
			header("Location: /admin/vestibulares/create");
			exit;
		}

		$generator = new SlugGenerator;

		$data['slug'] = $generator->generate($data['nome']);
		$data['idperson'] = $_SESSION['User']['idperson'];

		/* Valida se $_FILES existem com imagem */
		if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

			$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

			if (is_array($images) && !count($images)) {
				Vestibular::setError(self::$msgError);
				header("Location: /admin/vestibulares/create");
				exit;
			}

			$data['image'] = $images['image'];
			$data['image_thumb'] = $images['image_thumb'];
		}
		/* End */

		$vestibular = new Vestibular();
		$vestibular->setData($data);
		$vestibular->save();

		unset($_SESSION['recoversPost']);
		header("Location:/admin/vestibulares");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$vestibular = new Vestibular();
		$vestibular->get((int) $args['id']);

		if (file_exists($vestibular->getimage())) {
			unlink($vestibular->getimage());
			unlink($vestibular->getimage_thumb());
		}

		$vestibular->delete();
		header("Location: /admin/vestibulares");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$vestibular = new Vestibular();

		$vestibular->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("vestibulares-update", [
			"vestibular" => $vestibular->getValues(),
			'msgError' => Vestibular::getError()
		]);
		exit;
	}

	public function update(Request $request, Response $response, array $args)
	{

		$vestibular = new Vestibular();

		unset($_POST['_METHOD']);

		if (in_array("", $_POST)) {
			Vestibular::setError('Preencha todos os campos.');
			header("Location: /admin/vestibulares/" . $args['id']);
			exit;
		}

		$vestibular->get((int) $args['id']);

		/* Verifica se existe arquivo para ser enviado */
		if ($_FILES['image']['error'] == 0) {

			/*Primeiro apaga imagens anteriores*/
			if (file_exists($vestibular->getimage())) {
				unlink($vestibular->getimage());
				unlink($vestibular->getimage_thumb());
			}

			$images = parent::uploadImage($_FILES["image"], self::$path, self::$folder);

			if (!count((array) $images)) {
				Vestibular::setError(self::$msgError);
				header("Location: /admin/vestibulares/" . $args['id']);
				exit;
			}

			$_POST['image'] = $images['image'];
			$_POST['image_thumb'] = $images['image_thumb'];
		}

		$generator = new SlugGenerator;

		$_POST['slug'] = $generator->generate($_POST['nome']);
		$_POST['idperson'] = $_SESSION['User']['idperson'];

		$vestibular->setData($_POST);
		$vestibular->save();

		header("Location:/admin/vestibulares/" . $args['id']);
		exit;
	}
}//End Class