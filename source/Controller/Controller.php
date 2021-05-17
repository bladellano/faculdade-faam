<?php

namespace Source\Controller;

use Source\Model\User;
use CoffeeCode\Uploader\Image;

abstract class Controller
{
	/**
	 * [Verifica se o usuário está autenticado]
	 */

	public function __construct()
	{
			User::verifyLogin();
	}

	/**
	 * [uploadImage description]
	 * @param  [type]  $files     [$_FILES]
	 * @param  [type]  $path      [caminho]
	 * @param  [type]  $folder    [pasta]
	 * @param  integer $res_image [resolução da imagem principal]
	 * @param  integer $res_thumb [resolução do thumb de imagem]
	 * @return [type]             [Array com resultado de duas imagens maior e menor]
	 */
	public static function uploadImage($files, string $path, string $folder, $res_image = 1440, $res_thumb = 600)
	{

		$upload = new Image($path, $folder);
		$file = $files["image"];

		if (!empty($file["image"]) || !in_array($file["type"], $upload::isAllowed()))
			return [];

		return [
			'image_thumb' => $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), $res_thumb),
			'image' => $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), $res_image)
		];
	}

	/**
	 * [Paginção para os models]
	 * @param  [type] $model [Nome da class model]
	 * @param  [type] $uri   [Url para compor o link da paginação]
	 * @return [type]        [Retorna links das páginas, 
	 * Palavra chave e data do resultado]
	 */
	public function pagination($model, $uri)
	{
		$search = (isset($_GET['search'])) ? $_GET['search'] : "";
		$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

		if ($search != '') {
			$pagination = call_user_func_array(['Source\Model\\' . $model, 'getPageSearch'], [trim($search), $page]);
		} else {
			$pagination = call_user_func_array(['Source\Model\\' . $model, 'getPage'], [$page]);
		}

		$pages = [];

		for ($x = 0; $x <  $pagination['pages']; $x++) {
			array_push($pages, [
				'href' => "$uri?" . http_build_query([
					'page' => $x + 1,
					'search' => $search
				]),
				'text' => $x + 1
			]);
		}

		return [
			"pages" => $pages,
			"search" => $search,
			"data" => $pagination['data']
		];
	}
}
