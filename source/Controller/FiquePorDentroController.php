<?php

namespace Source\Controller;

use Source\DB\Sql;
use Source\PageAdmin;
use Source\Model\Page;

class FiquePorDentroController extends Controller
{
	public function index()
	{
		$p = new Page();
		$p->get(49); //Codigo página 'FIQUE POR DENTRO'
		$page = new PageAdmin();

		$disabled = [
			'title' => 1,
			'slug' => 1,
			'author' => 1,
		];

		$page->setTpl("pages-update", [
			"disabled" => $disabled,
			"p" => $p->getValues(),
			'msgError' => Page::getError(),
			'msgSuccess' => Page::getSuccess(),
			'scripts' => [
				'https://cdn.tiny.cloud/1/m01am2k1lnhc2p0m05kqb6l4a172n4yhae2hk29tszl381zp/tinymce/5/tinymce.min.js',
				'/views/admin/assets/js/form.js'
			]
		]);
		exit;
	}

	public static function getFiquePorDentro()
	{
		$sql = new Sql();
		return $sql->select('SELECT * FROM pages WHERE id = 49');
	}
}
