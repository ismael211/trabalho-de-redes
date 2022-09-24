<?php

include __DIR__ . '/../debug.php';
//include("mailer/class.phpmailer.php");
require_once('PHPMailer/PHPMailerAutoload.php');

function envia_Email($nome_cliente, $email_cliente1, $assunto, $mensagem)
{

	if (!filter_var(trim($email_cliente1), FILTER_VALIDATE_EMAIL)) {
		return "Error(1): ($email_cliente1) Email Principal inválido.";
	}

	require_once('config.php');
	$core = new IsistemCore();
	$core->Connect();
	$dados_empresa = $core->Fetch("SELECT * FROM empresa");
	$dados_sistema = $core->Fetch("SELECT * FROM sistema");


	$account = $dados_sistema['servidor_smtp_usuario'];
	$password = $dados_sistema['servidor_smtp_senha'];
	$from = $dados_empresa['email'];
	$from_name = $dados_empresa['nome'];
	$msg = $mensagem; // HTML message
	$subject = $assunto;

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->CharSet = 'iso-8859-1';
	$mail->Host = $dados_sistema['servidor_smtp'];
	$mail->SMTPAuth = true;
	$mail->Port = '587'; // Or 587
	$mail->Username = $account;
	$mail->Password = $password;
	$mail->SMTPSecure = $dados_sistema['smtp_enc'];
	$mail->From = $from;
	$mail->FromName = $from_name;
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $msg;
	$mail->addAddress(trim($email_cliente1), $nome_cliente);     // Add a recipient



	if ($mail->Send()) {
		$_SESSION['email_sent_to'] = $email_cliente1;
		return "ok";
	} else {

		return "Erro: - " . $mail->ErrorInfo;
	}
}
