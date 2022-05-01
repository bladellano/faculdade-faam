<?php

namespace Source\Controller;

use Source\DB\Sql;
use Source\Mailer;
use Source\PageAdmin;
use \Psr\Http\Message\ServerRequestInterface as Request;

class OuvidoriaController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $respostas = \Source\Model\Ouvidoria::getRespostas();

        $page = new PageAdmin();
        $page->setTpl("ouvidoria", array(
            "respostas" => $respostas,
        ));
        exit;
    }

    public function store(Request $request)
    {
        $lastId = (new Sql)->insert('ouv_respostas', $request->getParsedBody());

        if ($lastId) {
            $resposta = \Source\Model\Ouvidoria::getRespostas(["id"=>$lastId]);
            $resposta = $resposta[0];

            // ENVIAR EMAILS
            $addAddress = [
                "bladellano@gmail.com",
                "direcao@faam.com.br",
				"ouvidoria@faam.com.br",
				"dir.academica@faam.com.br"
            ];

            $mailer = new Mailer(
                'ouvidoria@faam.com.br',
                'Ouvidoria Faam',
                "Questionário Ouvidoria Faam", //Assunto
                "email-resposta-ouvidoria", //Template
                $resposta,
                $addAddress
            );

            $mailer->send();

            print(json_encode([
                'success' => true,
                'msg' => 'Enviou com sucesso!',
            ]));
            exit;
        } else {
            print(json_encode([
                'success' => false,
                'msg' => 'Falhou ao enviar!',
            ]));
            exit;
        }
    }
}
