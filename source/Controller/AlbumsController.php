<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\PhotoAlbum;
use Source\Model\Photo;

class AlbumsController extends Controller
{
	public static $msgError = "Selecione uma imagem válida.";
	public static $path = "storage/images";
	public static $folder = "albums";

	public function index()
	{
		$pg = $this->pagination('PhotoAlbum', '/admin/albums');
		$page = new PageAdmin();
		$page->setTpl("albums", array(
			"albums" => $pg['data'],
			"search" => $pg['search'],
			"pages" => $pg['pages']
		));exit;
	}

	public function destroyPhoto(Request $request, Response $response, array $args)
	{
		$photo = new Photo();
		$photo->get((int) $args["id_photo"]);
		$id_album = $photo->getid_photos_albums();

		$exist_cover = (new Photo())->getCoverFromAlbum($args["id_photo"]);

		if (count($exist_cover)) {
			PhotoAlbum::setError('Foto não poderá ser excluída, pois é capa do album.');
			header("Location: /admin/show-album/" . $args["id_album"]);
			exit;
		}

		if (file_exists($photo->getimage())) {
			unlink($photo->getimage());
			unlink($photo->getimage_thumb());
		}

		$photo->delete();
		header("Location: /admin/show-album/{$id_album}");
		exit;
	}

	public function changeCover(Request $request, Response $response, array $args)
	{
		$album = new PhotoAlbum();
		$album->get((int) $args["id_album"]);
		$album->setData(['id_photos_cover' => $args["id_photo"]]);
		$album->save();
		header("Location:/admin/show-album/{$args["id_album"]}");
		exit;
	}

	public function createNameAlbum(Request $request, Response $response, array $args)
	{
		$album = new PhotoAlbum();
		$exist = $album->verifyNameAlbum($request->getParsedBody()['album']);

		if (count($exist) != 0)
			die(json_encode(['success' => false, 'msg' => '&bull; Nome já existente na base dados!']));

		$album->setData($request->getParsedBody());
		$album->save();

		$allAlbums = $album->listAll();
		die(json_encode(['success' => true, 'msg' => 'Registrado com sucesso!', 'data' => $allAlbums]));
	}

	public function get(Request $request, Response $response, array $args)
	{
		$album = new PhotoAlbum();
		$page = new PageAdmin();

		$all_photos = $album->getPhotos((int) $args["id"]);
		$album->get((int) $args["id"]);

		$page->setTpl("show-albums", [
			"photos" => $all_photos,
			"album" => $album->getValues(),
			'msgError' => PhotoAlbum::getError()
		]);exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$albums = (new PhotoAlbum())->listAll();
		$page->setTpl("albums-create", [
			'msgError' => PhotoAlbum::getError(),
			'albums' => $albums
		]);exit;
	}

	public function store(Request $request, Response $response, array $args)
	{
		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRING);

		$files = $_FILES['images'];

		/* Normalização */
		for ($i = 0; $i < count($files["type"]); $i++) {
			foreach (array_keys($files) as $keys) {
				$all_images[$i][$keys] = $files[$keys][$i];
			}
		}

		$_SESSION['recoversPost'] = $request->getParsedBody();

		if (in_array("", $data)) {
			PhotoAlbum::setError('Preencha todos os campos.');
			header("Location: /admin/albums/create");
			exit;
		}

		foreach ($all_images as $file) {

			$images = parent::uploadImage(['image' => $file], self::$path, self::$folder);

			if (!count($images)) {
				PhotoAlbum::setError(self::$msgError);
				header("Location: /admin/albums/create");
				exit;
			}

			$data['image'] = $images['image'];
			$data['image_thumb'] = $images['image_thumb'];

			$photo = new Photo();
			$photo->setData($data);
			$photo->save();
		}

		unset($_SESSION['recoversPost']);
		header("Location:/admin/albums");
		exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$album = new PhotoAlbum();
		$album->get((int) $args["id"]);

		$all_photos = (new Photo())->getPhotosFromAlbum($args["id"]);

		foreach ($all_photos as $photo) {

			$p = new Photo();
			$p->get($photo['id']);

			if (file_exists($p->getimage())) {
				unlink($p->getimage());
				unlink($p->getimage_thumb());
			}
			$p->delete();
		}
		$album->delete();
		header("Location: /admin/albums");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$album = new PhotoAlbum();
		$album->get((int) $args['id']);
		$page = new PageAdmin();
		$page->setTpl("albums-update", [
			"album" => $album->getValues(),
			'msgError' => PhotoAlbum::getError(),
		]);exit;
	}

	public function update(Request $request, Response $response, array $args)
	{
		unset($_POST['_METHOD']);

		$album = new PhotoAlbum();
		$album->get((int) $args['id']);

		$album->setData($request->getParsedBody());
		$album->save();
		header("Location: /admin/albums");
		exit;
	}
}//End Class
