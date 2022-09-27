<?php

// include __DIR__ . '/../debug.php';
//include("mailer/class.phpmailer.php");
require_once('PHPMailer/PHPMailerAutoload.php');

function envia_Email($email, $assunto, $mensagem)
{

	if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
		return "Error(1): ($email) Email Principal inválido.";
	}

	require_once('config.php');
	$core = new IsistemCore();
	$core->Connect();

	$account = 'caiobes@2020.com.br';
	$password = '123456';
	$from = 'caiobes@2020.com.br';
	$from_name = 'GRUPO CIA';
	$msg = $mensagem; // HTML message
	$subject = $assunto;

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->CharSet = 'UTF-8';
	$mail->Host = 'localhost';
	$mail->SMTPAuth = true;
	$mail->Port = '25'; // Or 587
	$mail->Username = $account;
	$mail->Password = $password;
	$mail->SMTPSecure = 'ssl';
	$mail->From = $from;
	$mail->FromName = $from_name;
	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $msg;
	$mail->addAddress(trim($email), 'CIA');     // Add a recipient



	if ($mail->Send()) {
		$_SESSION['email_sent_to'] = $email;
		return "ok";
	} else {

		return "Erro: - " . $mail->ErrorInfo;
	}
}
