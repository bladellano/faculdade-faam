<?php

namespace Source\Controller;

use Source\Mailer;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class MailerController extends Controller
{
    public function __construct()
    {
    }

    public function sendFormContact()
    {

        $mailer = new Mailer(
            $_POST["email"],
            $_POST["name"],
            "Formulário de Contato do Site", //Assunto
            "email-sent", //Template
            $_POST
        );

        if ($mailer->send()) {
            die(json_encode(['success' => true, 'msg' => 'E-mail enviado com sucesso!']));
        }
        die(json_encode(['success' => false, 'msg' => 'Problemas ao enviar o e-mail!']));
    }
}