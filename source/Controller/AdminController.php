<?php

namespace Source\Controller;

use Source\PageAdmin;
use Source\Model\User;
use Source\Model\Article;

class AdminController
{
	public function deleteImage()
	{
		$sql = new \Source\DB\Sql();

		if (file_exists($_REQUEST["path"]))
			unlink($_REQUEST["path"]);

		try {
			$sql->query('DELETE FROM images WHERE id = :id', [":id" => $_REQUEST["id"]]);
			die(\json_encode(["success" => true, "msg" => "Imagem deletada com sucesso."]));
		} catch (\PDOException $e) {
			die(\json_encode(["success" => false, "msg" => $e->getMessage()]));
		}
	}

	public function	listImages()
	{
		$sql = new \Source\DB\Sql();
		$images = $sql->select("SELECT * FROM images ORDER BY id DESC");

		foreach ($images as &$file) {
			$file['type'] = explode(".", $file['path'])[1];
			$file['name'] = end( explode("/", $file['path']) );
		}
		
		$page = new PageAdmin([
			"header" => false,
			"footer" => false,
		]);
		$page->setTpl("list-images", [
			"images" => $images
		]);
		exit;
	}

	public function sendImage()
	{

		$upload = new \CoffeeCode\Uploader\Image("storage/images", "detached");
		$general_file = new \CoffeeCode\Uploader\File("storage/files", "pdf");
		$sql = new \Source\DB\Sql();

		$file = $_FILES["file_send_image"];

		if ($_FILES['file_send_image']['error'] == 4)
			die(\json_encode(["success" => false, "msg" => "A ação gerou um problema. Por favor selecione uma imagem ou PDF."]));

		if ($file['type'] === 'application/pdf') {
			$uploaded = $general_file->upload($file, pathinfo($file["name"], PATHINFO_FILENAME));
		} else {
			$uploaded = $upload->upload($file, pathinfo($file["name"], PATHINFO_FILENAME), 600);
		}

		$id = $sql->insert("images", ["path" => $uploaded]);

		if ($id > 0) {
			die(\json_encode(["success" => true, "msg" => "Imagem/Arquivo gravado(a) com sucesso."]));
		} else {
			die(\json_encode(["success" => false, "msg" => "Erro ao registrar imagem."]));
		}
		exit;
	}

	public function index()
	{
		User::verifyLogin();
		$articles  = count(Article::listAll());
		
		$page = new PageAdmin();
		$page->setTpl("index", [
			"qtdArticles" => $articles,
		]);
		exit;
	}

	public function logging()
	{
		try {
			User::login($_POST["login"], $_POST["password"]);
		} catch (\Exception $e) {
			
			User::setError($e->getMessage());
			header("Location: /admin/login");
			exit;
		}
		header("Location: /admin");
		exit;
	}

	public function screen()
	{
		$page = new PageAdmin([
			"header" => false,
			"footer" => false,
		]);
		$page->setTpl("login", [
			"msgError" => User::getError()
		]);
		exit;
	}

	public function logout()
	{
		User::logout();
		header("Location: /admin/login");
		exit;
	}
}
