<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\User;

class UsersController extends Controller
{
	public function index()
	{
		$page = new PageAdmin();
		$pg = $this->pagination('User','/admin/users');
		$page->setTpl("users", array(
			"users" => $pg['data'],
			"search" => $pg['search'],
			"pages"=> $pg['pages']
		));exit;
	}
	
	public function changePassword(Request $request, Response $response, array $args)
	{
		$iduser = $args['id'];

		$user = new User();
		$user->get((int)$iduser);
		$page = new PageAdmin();
		$page->setTpl("users-password",[
			'user'=>$user->getValues(),
			'msgError'=>User::getError(),
			'msgSuccess'=>User::getSuccess()
		]);exit;
	}

	public function updatePassword(Request $request, Response $response, array $args)
	{
		$_POST = $request->getParsedBody();
		$iduser = $args['id'];

		if(!isset($_POST['despassword']) || $_POST['despassword']===''){
			User::setError('Preencha a nova senha.');
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		if(!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm']===''){
			User::setError('Preencha a confirmação da nova senha.');
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		if($_POST['despassword'] != $_POST['despassword-confirm']){
			User::setError('Confirme corretamente as senhas.');
			header("Location: /admin/users/$iduser/password");
			exit;
		}

		$user = new User();
		$user->get((int)$iduser);
   		//Ele já salva no banco, não precisa do save();
		$user->setPassword(User::getPasswordHash($_POST['despassword']));

		User::setSuccess('Senha alterada com sucesso.');
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	public function create()
	{
		$page = new PageAdmin();
		$page->setTpl("users-create",[
			'msgError'=>User::getError(),
			'msgSuccess'=>User::getSuccess()
		]);exit;
	}

	public function destroy(Request $request, Response $response, array $args)
	{
		$iduser = $args['id'];
		$user = new User();
		$user->get((int) $iduser);
		// die;

		$user->delete();
		header("Location: /admin/users");
		exit;
	}

	public function edit(Request $request, Response $response, array $args)
	{
		$iduser = $args['id'];
		$user = new User();
		$user->get((int) $iduser);
		$page = new PageAdmin();
		$page->setTpl("users-update", [
			"user" => $user->getValues(),
		]);exit;
	}

	public function store(Request $request, Response $response, array $args)
	{

		$data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRING);

		if (in_array("", $data)) {
			User::setError('Preencha todos os campos.');
			header("Location: /admin/users/create");
			exit;
		}
		$user = new User();
		$data['inadmin'] = (isset($data['inadmin'])) ? 1 : 0;
		$user->setData($data);
		$user->save();
		header("Location:/admin/users");
		exit;
	}

	public function update(Request $request, Response $response, array $args)
	{
		$_POST = $request->getParsedBody();
		$iduser = $args['id'];

		$user = new User();
		$_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;
		$user->get((int) $iduser);
		$user->setData($_POST);
		$user->update();
		header("Location: /admin/users");
		exit;
	}

	public function listAll(Request $request, Response $response, array $args)
	{
		$users = User::listAll();
		return $response->withJson($users);
	}

}