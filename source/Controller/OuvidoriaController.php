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

        $data = $request->getParsedBody();
        $captcha = $data['g-recaptcha-response'];

        // $res = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Le1mbcZAAAAAAnWnOCN7kS6xueKw82MQifMXw76&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']));

        if(!$captcha){
            print(json_encode([
                'success' => false,
                'msg' => 'Captcha não preenchido!',
            ]));
            exit;
        }

        unset($data['g-recaptcha-response']);

        $lastId = (new Sql)->insert('ouv_respostas', $data);

        if ($lastId) {
            $resposta = \Source\Model\Ouvidoria::getRespostas(["id"=>$lastId]);
            $resposta = $resposta[0];

            /** E-mails que irão receber os forms respondidos */
            $addAddress = [
                "bladellano@gmail.com",
                "direcao@faam.com.br",
				"ouvidoria@faam.com.br",
				"dir.academica@faam.com.br"
            ];

            $mailer = new Mailer(
                'ouvidoria@faam.com.br',
                'Ouvidoria Faam',
                "Mensagens da Ouvidoria", //Assunto
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
