<?php

namespace Source;

use \Rain\Tpl;
use \PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
	const USERNAME = MAIL_EMAIL;
	const PASSWORD = MAIL_PASSWORD;
	const NAME_FROM = MAIL_NAME_FROM;
	const HOST = MAIL_HOST;

	private $mail;

	public function __construct($fromAdress, $fromName, $subject, $tplName, $data = array())
	{

		$config = array(
			"tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views/email/",
			"cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
			"debug" => false,
		);

		Tpl::configure($config);

		$tpl = new Tpl;

		foreach ($data as $key => $value) {
			$tpl->assign($key, $value);//Seta as variáveis dentro do template.
		}
		
		$html = $tpl->draw($tplName, true); //Coloca o conteudo dentro da variavel $html.
		$this->mail = new PHPMailer();
		$this->mail->isSMTP();
		$this->mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			 )
			);
		$this->mail->SMTPDebug = 0;
		$this->mail->Debugoutput = 'html';
		$this->mail->Host = Mailer::HOST;
		$this->mail->Port = 587;
		$this->mail->SMTPSecure = 'tls';
		$this->mail->SMTPAuth = true;
		$this->mail->Username = Mailer::USERNAME;
		$this->mail->Password = Mailer::PASSWORD;
		$this->mail->setFrom($fromAdress, $fromName);
		$this->mail->addAddress(Mailer::USERNAME, Mailer::NAME_FROM);
		$this->mail->Subject = utf8_decode( $subject );
		$this->mail->msgHTML($html);
		$this->mail->AltBody = 'This is a plain-text message body';
	}

	public function send()
	{
		return $this->mail->send();
	}
}