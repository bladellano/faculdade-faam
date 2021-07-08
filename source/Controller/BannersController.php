<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Banner;

class BannersController extends Controller
{
	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "banners";

	public function index()
	{
		$pg = $this->pagination('Banner', '/admin/banners');
		$page = new PageAdmin();
		$page->setTpl("banners", array(
			"banners" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages']
		));
		exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$page->setTpl("banners-create", [
			'msgError' => Banner::getError(),
			'scripts' => ['https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRIPPED);

		$data['bool_cor_text'] = !empty($data['bool_cor_text']) ? 1 : 0;
		$data['bool_banner_clicked'] = !empty($data['bool_banner_clicked']) ? 1 : 0;

		$_SESSION['recoversPost'] = $data;

		/* Campos obrigatórios no banner são 'slug' e 'arquivo de imagem' */
		if ($data['slug'] == "" || $_FILES['image']['error'] == 4) {
			Banner::setError('Preencha os campos obrigatórios (*)');
			header("Location: /admin/banners/create");
			exit;
		}

		/* Valida se $_FILES existem com imagem */
		if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

			$images = parent::uploadImage($_FILES['image'], self::$path, self::$folder);

			if (is_array($images) && !count($images)) {
				Banner::setError(self::$msgError);
				header("Location: /admin/banners/create");
				exit;
			}

			$data['image'] = $images['image'];
			$data['image_thumb'] = $images['image_thumb'];
		} /* End */

		$banner = new Banner();
		$banner->setData($data);
		$banner->save();
		unset($_SESSION['recoversPost']);
		header("Location:/admin/banners");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$banner = new Banner();
		$banner->get((int) $args['id']);

		if (file_exists($banner->getimage())) {
			unlink($banner->getimage());
			unlink($banner->getimage_thumb());
		}

		$banner->delete();
		header("Location: /admin/banners");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$banner = new Banner();
		$banner->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("banners-update", [
			"banner" => $banner->getValues(),
			'msgError' => Banner::getError(),
			'scripts' => ['https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js', '/views/admin/assets/js/form.js']
		]);
		exit;
	}

	public function update(Request $request, Response $response, array $args)
	{

		$banner = new Banner();
		$banner->get((int) $args['id']);
		unset($_POST['_METHOD']);

		$_POST['bool_cor_text'] = !empty($_POST['bool_cor_text']) ? 1 : 0;
		$_POST['bool_banner_clicked'] = !empty($_POST['bool_banner_clicked']) ? 1 : 0;

		/* Verifica se existe arquivo para ser enviado */
		if ($_FILES['image']['error'] == 0) {

			/*Primeiro apaga imagens anteriores*/
			if (file_exists($banner->getimage())) {
				unlink($banner->getimage());
				unlink($banner->getimage_thumb());
			}

			$images = parent::uploadImage($_FILES, self::$path, self::$folder);

			if (is_array($images) && !count($images)) {
				Banner::setError(self::$msgError);
				header("Location: /admin/banners/" . $args['id']);
				exit;
			}

			$_POST['image'] = $images['image'];
			$_POST['image_thumb'] = $images['image_thumb'];
		}

		$banner->setData($_POST);
		$banner->save();

		header("Location:/admin/banners/" . $args['id']);
		exit;
	}
}//End Class