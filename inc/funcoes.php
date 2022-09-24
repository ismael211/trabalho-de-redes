<?php
//header("Content-Type: text/html;  charset=ISO-8859-1",true);
// error_reporting(0);
// ini_set("display_errors", "Off");
//////////////////////////////////////////////////////////////////////////
// Isistem Gerenciador Financeiro para Hosts  		                    //
// Descriï¿½ï¿½o: Sistema de Gerenciamento de Clientes		                //
// Site: www.isistem.com.br       										//
//////////////////////////////////////////////////////////////////////////
include __DIR__.'/../debug.php';
// require_once('./inc/config.php');
require_once('class.xmlapi.php');
require_once('class.whois.php');
require_once('class.mail.php');
require_once ('Crypt.php');

$getIncludedFiles = get_included_files();
$temInclude = false;
foreach ($getIncludedFiles as $filename) {
    preg_match("/config.php/i", $filename, $matches);
    if ($matches) {
        $temInclude = true;
    }
}
if (!$temInclude) {
	include(__DIR__.'/config.php');
}

$core = new IsistemCore();
$core->Connect();

// Uso global
$dados_empresa = $core->Fetch("SELECT * FROM empresa");
$dados_sistema = $core->Fetch("SELECT * FROM sistema");

// Funï¿½ï¿½o para remover acentos
function remove_acentos($msg) {
	$a = array("/[ï¿½ï¿½ï¿½ï¿½ï¿½]/"=>"A","/[ï¿½ï¿½ï¿½ï¿½ï¿½]/"=>"a","/[ï¿½ï¿½ï¿½ï¿½]/"=>"E","/[ï¿½ï¿½ï¿½ï¿½]/"=>"e","/[ï¿½ï¿½ï¿½ï¿½]/"=>"I","/[ï¿½ï¿½ï¿½ï¿½]/"=>"i","/[ï¿½ï¿½ï¿½ï¿½ï¿½]/"=>"O",	"/[ï¿½ï¿½ï¿½ï¿½ï¿½]/"=>"o","/[ï¿½ï¿½ï¿½ï¿½]/"=>"U","/[ï¿½ï¿½ï¿½ï¿½]/"=>"u","/ï¿½/"=>"c","/ï¿½/"=> "C");

	return preg_replace(array_keys($a), array_values($a), $msg);
}

// Funï¿½ï¿½o para retornar o tipo de medida do tamanho do arquivo(Byts, Kbytes, Megabytes, Gigabytes, etc...)
function tamanho_arquivo($size){
	$i=0;
	$iec = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	while (($size/1024)>1) {
		$size=$size/1024;
		$i++;
	}
	return substr($size,0,strpos($size,'.')+3).$iec[$i];
}

function tamanho_arquivo2($size){
	$i=0;
	$iec = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	while (($size/1024)>1) {
		$size=$size/1024;
		$i++;
	}
	return $size.$iec[$i];
}

// Funï¿½ï¿½o para retornar o tamanho do plano(espaï¿½o e trafego) em Megabytes, Gigabytes, etc...
function tamanho_plano($size){
	$i=0;
	$iec = array(" MB", " GB", " TB");
	while (($size/1000)>1) {
		$size=$size/1000;
		$i++;
	}
	return substr($size,0,strpos($size,'.')+4).$iec[$i];
}

// Funï¿½ï¿½o para gerar o ticket_id dos tickets
function gerarCodigo($letras,$numeros) {
	$lista_letras = 'ABCDSEGHIJKLMNOPQRSTUVXYWZ';
	$lista_numeros = '0123456789';
	$codigo_letras = '';
	$codigo_numeros = '';
	$i = 0;
	$ii = 0;
	while ($i < $letras) {
		$codigo_letras .= substr($lista_letras, mt_rand(0, strlen($lista_letras)-1), 1);
		$i++;
	}
	while ($ii < $numeros) {
		$codigo_numeros .= substr($lista_numeros, mt_rand(0, strlen($lista_numeros)-1), 1);
		$ii++;
	}
	return $codigo_letras."-".$codigo_numeros;
}

//Gera um GUID

function guid()
{
	if (function_exists('com_create_guid') === true)
	{
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

// Funï¿½ï¿½o para calcular o tempo passado entre duas datas
function tempo($starttime, $endtime){
	$starttime=strtotime($starttime);

	$endtime=strtotime($endtime);

	$timediff = $endtime-$starttime;
	$days=intval($timediff/86400);
	$remain=$timediff%86400;
	$hours=intval($remain/3600);
	$remain=$remain%3600;
	$mins=intval($remain/60);
	$secs=$remain%60;
	if (strlen($mins)<2) {
		$mins = '0'.$mins;
	}
	if($days > 0) $dia = $days.'d';
	if($hours > 0) $hora = $hours.'h';
	if($mins > 0) $minuto = $mins.'m';
	$segundo = $secs.'s';
	$timediff = $dia.$hora.$minuto.$segundo;
	return $timediff;
}

function conta_dias($data_inicio, $data_fim){
	//$now = time(); // or your date as well
	$data_fim = strtotime($data_fim);
	$datediff = $data_inicio - $data_fim;

	return str_replace("-", "", floor($datediff / (60 * 60 * 24)));
}

// Funï¿½ï¿½o para trocar a sigla do Estado pelo nome completo do Estado
function sigla_estado($estado) {



	$estado = str_replace("Acre","AC",$estado);
	$estado = str_replace("Amapá","AP",$estado);
	$estado = str_replace("Alagoas","AL",$estado);
	$estado = str_replace("Amazonas","AM",$estado);
	$estado = str_replace("Bahia","BA",$estado);
	$estado = str_replace("Ceará","CE",$estado);
	$estado = str_replace("Distrito Federal","DF",$estado);
	$estado = str_replace("Espírito Santo","ES",$estado);
	$estado = str_replace("Goiás","GO",$estado);
	$estado = str_replace("Maranhão","MA",$estado);
	$estado = str_replace("Mato Grosso do Sul","MS",$estado);
	$estado = str_replace("Mato Grosso","MT",$estado);
	$estado = str_replace("Minas Gerais","MG",$estado);
	$estado = str_replace("Pará","PA",$estado);
	$estado = str_replace("Paraná","PR",$estado);
	$estado = str_replace("Paraíba","PB",$estado);
	$estado = str_replace("Pernambuco","PE",$estado);
	$estado = str_replace("Piauí","PI",$estado);
	$estado = str_replace("Rio de Janeiro","RJ",$estado);
	$estado = str_replace("Rio Grande do Norte","RN",$estado);
	$estado = str_replace("Rio Grande do Sul","RS",$estado);
	$estado = str_replace("Rondônia","RO",$estado);
	$estado = str_replace("Roraima","RR",$estado);
	$estado = str_replace("Santa Catarina","SC",$estado);
	$estado = str_replace("Sitío Paulo","SP",$estado);
	$estado = str_replace("Sergipe","SE",$estado);
	$estado = str_replace("Tocantins","TO",$estado);

	return $estado;
}
// Funï¿½ï¿½o para consultar o valor do serviï¿½o adicional
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_servico" && !empty($_GET[codigo_servico])){

	$dados_servico = $core->Fetch("SELECT * FROM servicos_modelos where codigo = '".$_GET[codigo_servico]."'");
	echo number_format($dados_servico[valor],2,",",".")."&".$dados_servico[descricao]."&".$dados_servico[valor];
	exit();
}

// Funï¿½ï¿½o para consultar usuario no banco de dados e retornar se jï¿½ existe ou não
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_usuario_cpanel" && !empty($_GET[usuario_cpanel])){
	$total_usuarios = $core->Fetch("SELECT * FROM dominios where usuario_cpanel = '".$_GET[usuario_cpanel]."'");
	if($total_usuarios == 0) {
		echo "ok";
	}
	exit();
}
// Funï¿½ï¿½o para pesquisar se o dominio jï¿½ esta cadastrado
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_dominio" && !empty($_GET[dominio])){
	$total_dominios = $core->Fetch("SELECT * FROM dominios where dominio = '".$_GET[dominio]."'");
	if($total_dominios == 0) {
		echo "ok";
	}
	exit();
}
// Funï¿½ï¿½o para consultar whois para domï¿½nio e retornar se jï¿½ estï¿½ registrado ou não
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_dominio_whois" && !empty($_GET[dominio])){
	$resultado1 = ver_whois($_GET[dominio]);
	$resultado = explode(" ",$resultado1);
	if ($resultado[0] == 1) { echo "1"; } elseif($resultado[0] == 0) { echo "0"; }
	exit();
}

// Funï¿½ï¿½o para consultar whois para domï¿½nio e retornar se jï¿½ estï¿½ registrado ou não
if(isset($_GET[acao]) && $_GET[acao] == "comparar_usuario_senha" && !empty($_GET[usuario]) && !empty($_GET[senha])){
	if(!eregi($_GET[usuario],$_GET[senha])) {
		echo "ok";
	}
	exit();
}
// Alterar senha do domï¿½nio na central do cliente e no cpanel/whm
if(isset($_GET[acao]) && $_GET[acao] == "alterar_senha" && !empty($_GET[senha]) && !empty($_GET[dominio])){
	$dados_dominio = $core->Fetch("SELECT * FROM dominios where codigo = '".$_GET[dominio]."'");
	$dados_cliente = $core->Fetch("SELECT * FROM dominios where codigo = '".$dados_dominio[codigo_cliente]."'");
	$dados_plano = $core->Fetch("SELECT * FROM planos where codigo = '".$dados_dominio[codigo_plano]."'");
	$dados_servidor = $core->Fetch("SELECT * FROM servidores where codigo = '".$dados_plano[codigo_servidor]."'");

	if($dados_plano[whm] == 'sim' && $dados_servidor[whm] == 'sim') {

		$resultado = changepass($dados_servidor[ip],$dados_servidor[usuario_whm],$dados_servidor[accesshash],'0',$_GET[senha],$dados_dominio[usuario_cpanel]);

		if (!eregi("has been changed",$resultado)){
			echo "erro";
		} else {
			$query = $core->Prepare("UPDATE dominios SET senha_cpanel='".$_GET[senha]."' where codigo='".$dados_dominio[codigo]."'");
			$result = $query->Execute();
			echo $_GET[senha];
		}
	} else {
		$query = $core->Prepare("UPDATE dominios SET senha_cpanel='".$_GET[senha]."' where codigo='".$dados_dominio[codigo]."'");
		$result = $query->Execute();
		echo $_GET[senha];
	}

	exit();
}
// Alterar senha do domï¿½nio na central do cliente e no cpanel/whm
if(isset($_GET[acao]) && $_GET[acao] == "alterar_senha_cliente" && !empty($_GET[senha]) && !empty($_GET[codigo_cliente])){
	$dados_cliente = $core->Fetch("SELECT * FROM clientes where codigo = '".$_GET[codigo_cliente]."'");

	$query = $core->Fetch("UPDATE clientes SET senha='".$_GET[senha]."' where codigo='".$dados_cliente[codigo]."'");
	$result = $query->Execute();
	echo $_GET[senha];

	exit();
}
// Funï¿½ï¿½o para recuperar senha do usuario
if(isset($_GET[acao]) && $_GET[acao] == "recuperar_senha" && !empty($_GET[usuario]) && !empty($_GET[tipo])){

	if($_GET[tipo] == 'cliente') {
		$qusu = "SELECT * FROM clientes where email1 = '".$_GET[usuario]."'";
		$total_cliente = $core->RowCount($qusu);

		$ausu = $core->Fetch($qusu);


		if($total_cliente == 0) {
			echo "erro_usuario";
			exit();
		} else {
		/*
			$gera = substr(md5(rand(0,9)), 0, 7);

			$senha = criptSenha($_GET[usuario],$gera);


		mysql_query("UPDATE clientes SET senha = $senha WHERE email1 = '".$_GET[usuario]."'");
		*/


		require_once('Crypt.php');



		$crypt = new Crypt();
		$crypt->Mode = Crypt::MODE_HEX;
		$crypt->Key  = '!@#$%&*()_+?:';
		$decrypted = $crypt->decrypt($ausu['senha']);


		$mensagem =

		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<link href="/admin/inc/boots.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		</head>
		<body>
		<center>
		<div class="panel" style="width:500px;height:auto; padding:16px;">
		<h2>Recuperar senha</h2>

		<br />
		<br />
		<p>Usuário/User: '.$_GET[usuario].'</p>
		<br />
		<p>Sua senha/Your password: '.$decrypted.'</p>
		</div>
		</center>
		</body>
		</html>';

		envia_Email($ausu[nome],$ausu[email1],$ausu[email2],"Recuperar Senha de Acesso",$mensagem);

		echo "ok";
	}
}elseif($_GET[tipo] == 'sub') {
	$qusu = "SELECT * FROM subcontatos where usuario = '".$_GET[usuario]."'";
	$total_cliente = $core->RowCount($qusu);

	$ausu = $core->Fetch($qusu);


	if($total_cliente == 0) {
		echo "erro_usuario";
		exit();
	} else {
		/*
			$gera = substr(md5(rand(0,9)), 0, 7);

			$senha = criptSenha($_GET[usuario],$gera);


		mysql_query("UPDATE clientes SET senha = $senha WHERE email1 = '".$_GET[usuario]."'");
		*/


		$decrypted = decript_complex($ausu['senha']);


		$mensagem =

		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<link href="/admin/inc/boots.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		</head>
		<body>
		<center>
		<div class="panel" style="width:500px;height:auto; padding:16px;">
		<h2>Recuperar senha</h2>

		<br />
		<br />
		<p>Usuário/User: '.$_GET[usuario].'</p>
		<br />
		<p>Sua senha/Your password: '.$decrypted.'</p>
		</div>
		</center>
		</body>
		</html>';

		envia_Email($ausu[nome],$ausu[email],NULL,"Recuperar Senha de Acesso",$mensagem);

		echo "ok";
	}
} else {

	$total_operador = $core->RowCount("SELECT * FROM operadores where login = '".$_GET[usuario]."'");

	if($total_operador == 0) {
		echo "erro_usuario";
		exit();
	} else {

		$usuario = $_GET[usuario];

		require_once('Crypt.php');


		$crypt = new Crypt();
		$crypt->Mode = Crypt::MODE_HEX;
		$crypt->Key  = '!@#$%&*()_+?:';
		$decrypted = $crypt->decrypt($ausu['senha']);


		$mensagem = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<link href="/admin/inc/boots.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		</head>
		<body>
		<center>
		<div class="panel" style="width:500px;height:auto; padding:16px;">
		<h2>Recuperar senha</h2>

		<br />
		<br />
		<p>Usuário/User: '.$_GET[usuario].'</p>
		<br />
		<p>Sua senha/Your password: '.$decrypted.'</p>
		</div>
		</center>
		</body>
		</html>';

		envia_Email($dados_operador[nome],$dados_operador[email],"","Recuperar Senha de Acesso",$mensagem);

		echo "ok";
	}
}
exit();
}
// Funï¿½ï¿½o para buscar tickets
if(isset($_GET[acao]) && $_GET[acao] == "buscar_tickets" && !empty($_GET[chave]) && !empty($_GET[cat])){
	$resultado = $core->Fetch("SELECT * FROM tickets where usuario_cpanel = '".$_GET[usuario_cpanel]."'");
	echo $resultado;
	exit();
}
// Funï¿½ï¿½o para atualizar o bloco de notas do usuario
if(isset($_POST[acao]) && $_POST[acao] == "atualizar_bloco_notas" && !empty($_POST[notas]) && !empty($_SESSION["login_sistema"])){
	$operador = $_SESSION["login_sistema"];
	$notas = utf8_decode($_POST["notas"]);
	$data = date("d/m/Y H:i:s");

	$editar_notas = "UPDATE operadores set bloco_notas = '$notas', bloco_data = NOW() where login = '$operador'";
	$query = $core->Prepare($editar_notas);
	$result = $query->execute();

	echo "ok"."&".$data;
	exit();
}
// Funï¿½ï¿½o para alterar o perfil do operador
if(isset($_POST[acao]) && $_POST[acao] == "alterar_perfil" && !empty($_POST[nome]) && !empty($_POST[email]) && !empty($_POST[senha])){
	$operador = $_SESSION["login_sistema"];
	$nome = utf8_decode($_POST["nome"]);
	$email = $_POST[email];
	$senha = $_POST[senha];

	$editar_perfil = "UPDATE operadores set nome = '$nome', email = '$email', senha = '$senha' where login = '$operador'";
	$query = $core->Prepare($editar_perfil);
	$result = $query->execute();

	echo "ok";
	exit();
}
// Funï¿½ï¿½o consultar mensagem prï¿½-definida e inserir no formulario
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_mensagem_pre" && !empty($_GET[codigo])){
	$dados_mensagem = $core->Fetch("SELECT * FROM tickets_mensagens_pre where codigo = '".$_GET[codigo]."'");

	echo $dados_mensagem[mensagem];
	exit();
}
// Funï¿½ï¿½o consultar artigo e inserir no formulario
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_artigo" && !empty($_GET[codigo])){
	$dados_artigo = $core->Fetch("SELECT * FROM tickets_faq where codigo = '".$_GET[codigo]."'");

	echo $dados_artigo[texto];
	exit();
}
// Funï¿½ï¿½o inserir/remover um tipo de domï¿½nio
if(isset($_GET[acao]) && $_GET[acao] == "inserir_tipo_dominio" && !empty($_GET[tipo])){

	$query = $core->Prepare("INSERT INTO tipos_dominio (nome) VALUES ('".$_GET[tipo]."')");
	$result = $query->Execute();
	if (!$result) {
		$erro = $core->ErrorInfo();
		die($erro[2]);
	}

	echo "ok"."&"."inserir_tipo_dominio";

	exit();
}
if(isset($_GET[acao]) && $_GET[acao] == "remover_tipo_dominio" && !empty($_GET[tipo])){
	$dados_tipo = $core->Fetch("SELECT * FROM tipos_dominio where nome = '".$_GET[tipo]."'");

	if($dados_tipo[bloqueado] == 'sim') {
		echo "bloqueado"."&"."remover_tipo_dominio";
	} else {
		$query = $core->Query("DELETE From tipos_dominio Where nome = '".$_GET[tipo]."'");
		echo "ok"."&"."remover_tipo_dominio";
	}
	exit();
}
// Funï¿½ï¿½o inserir/remover um grupo de plano
if(isset($_GET[acao]) && $_GET[acao] == "grupo_plano" && !empty($_GET[opcao]) && !empty($_GET[grupo])){

	if($_GET[opcao] == 'adicionar') {
		$core->Query("INSERT INTO planos_grupos (grupo) VALUES ('".$_GET[grupo]."')");
		echo "ok|adicionar|".$_GET[grupo]."";
	} else {
		$core->Query("DELETE From planos_grupos Where grupo = '".$_GET[grupo]."'");
		echo "ok|remover|".$_GET[grupo]."";
	}

	exit();
}
// Funï¿½ï¿½o para testar integraï¿½ï¿½o do sistema/servidor com o WHM/cPanel
if(isset($_POST[acao]) && $_POST[acao] == "testar_integracao_whm" && !empty($_POST[host]) && !empty($_POST[usuario]) && !empty($_POST[accesshash])){

	$xmlapi = new xmlapi($_POST[host]);
	$xmlapi->hash_auth($_POST[usuario],$_POST[accesshash]);
	$xmlapi->set_output("json");

	$resultado_versao = json_decode($xmlapi->version());
	$resultado_hostname = json_decode($xmlapi->gethostname());

	echo $resultado_versao->{"version"}."|".$resultado_hostname->{"hostname"};

	exit();
}
// Funï¿½ï¿½o apra checar valor do registro de dominio
if(isset($_GET[acao]) && $_GET[acao] == "registro_dominio" && !empty($_GET[codigo_extensao])){
	$dados_extensao = $core->Fetch("SELECT * FROM precos_dominios where codigo = '".$_GET[codigo_extensao]."'");

	echo number_format($dados_extensao[valor],2,",",".");

	exit();
}
// Funï¿½ï¿½o para pesquisar valor do domï¿½nio para cadastro de fatura avulsa
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_valor_dominio_fatura_avulsa" && !empty($_GET[codigo_dominio])){
	$dados_dominio = $core->Fetch("SELECT * FROM dominios where codigo = '".$_GET[codigo_dominio]."'");

	echo number_format($dados_dominio[valor],2,",",".");

	exit();
}
// Funï¿½ï¿½o para bloquear IP da assinatura
if(isset($_GET[acao]) && $_GET[acao] == "bloquear_ip" && !empty($_GET[ip])){
	$dados_empresa = $core->Fetch("SELECT * FROM empresa");

	if(ereg($_GET[ip],$dados_empresa[assinatura_ips_banidos])) {
		echo "ok1";
		exit();
	} elseif(!ereg($_GET[ip],$dados_empresa[assinatura_ips_banidos])) {

		if($dados_empresa[assinatura_ips_banidos] == '') {
			$assinatura_ips_banidos = $_GET[ip];
		} else {
			$assinatura_ips_banidos = $dados_empresa[assinatura_ips_banidos].",".$_GET[ip];
		}

		$core->Query("UPDATE empresa set assinatura_ips_banidos = '$assinatura_ips_banidos' where codigo = '1'");
		echo "ok";
		exit();
	} else {
		echo "erro";
		exit();
	}
}
// Funï¿½ï¿½o para formatar moeda(R$)
if(isset($_GET[acao]) && $_GET[acao] == "number_format" && !empty($_GET[valor])){

	echo number_format($_GET[valor],2,",",".");

	exit();
}
// Funï¿½ï¿½o para retornar o nome do mï¿½s
function mes_nome($a) {
	switch($a) {
		case 1: $mes = "Janeiro"; break;
		case 2: $mes = "Fevereiro"; break;
		case 3: $mes = "Março"; break;
		case 4: $mes = "Abril"; break;
		case 5: $mes = "Maio"; break;
		case 6: $mes = "Junho"; break;
		case 7: $mes = "Julho"; break;
		case 8: $mes = "Agosto"; break;
		case 9: $mes = "Setembro"; break;
		case 10: $mes = "Outubro"; break;
		case 11: $mes = "Novembro"; break;
		case 12: $mes = "Dezembro"; break;
	}
	return $mes;
}
// Funï¿½ï¿½o para exibir logs do sistema
if(isset($_GET[acao]) && $_GET[acao] == "exibir_log_sistema" && !empty($_GET[codigo])){
	$dados_log = $core->Fetch("SELECT * FROM logs_sistema where codigo = '".$_GET[codigo]."'");

	list($data,$hora) = explode(" ",$dados_log[data_hora]);
	list($ano,$mes,$dia) = explode("-",$data);
	$data_hora = $dia."/".$mes."/".$ano." ".$hora;

	if(ereg("<pre>",$dados_log['log'])) {
		$log = $dados_log['log'];
	} else {
		$log = "<pre>".$dados_log['log']."</pre>";
	}

	echo "<strong>".$dados_log['descricao']."</strong><br><br>Data/Hora: ".$data_hora."<br>Status: ".$dados_log['tipo']."<br><br>".$log."";

	exit();
}

// Funï¿½ï¿½o para remover campos adicionais do domï¿½nio
if(isset($_GET[acao]) && $_GET[acao] == "remover_campo_adicional" && !empty($_GET[codigo])){
	$total_campos_valores = $core->RowCount("SELECT * FROM campos_adicionais_valores where codigo = '".$_GET[codigo]."'");

	if($total_campos_valores > 0) {
		$core->Query("DELETE From campos_adicionais_valores Where codigo = '".$_GET[codigo]."'");
		echo "ok";
	}

	exit();
}

// Funï¿½ï¿½o para criar cï¿½lulas de logs do sistema
function Criar_Celula_Log($log,$tipo) {

	if($tipo == "ok") {
		$celula_log = "<div class=\"ui success message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Concluído!
		</div>
		<div class=\"list\">
		<p>".$log."</p>
		</div>
		</div>";

	} elseif($tipo == 'ok2') {
		$celula_log = "<div class=\"ui success message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Atenção!
		</div>
		<div class=\"list\">
		<p>".$log."</p>
		</div>
		</div>";
	} elseif($tipo == 'alerta' || $tipo == 'erro') {
		$celula_log = "<div class=\"ui warning message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Atenção!
		</div>
		<div class=\"list\">
		<p>".$log."</p>
		</div>
		</div>";

	} else {
		$celula_log = "<div class=\"ui info message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Atenção!
		</div>
		<div class=\"content\">
		<ul>".$log."</ul>
		</div>
		</div>";

	}

	return $celula_log;
}

function alerta_ul($log,$tipo) {

	if($tipo == "sucesso") {
		$celula_log = "<div class=\"ui success message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Concluído!
		</div>
		<div class=\"content\">
		<ul>".$log."</ul>
		</div>
		</div>";

	} elseif($tipo == 'alerta') {
		$celula_log = "<div class=\"ui warning message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Concluído, mas com erros!
		</div>
		<div class=\"content\">
		<ul>".$log."</ul>
		</div>
		</div>";
	} elseif($tipo == 'erro') {
		$celula_log = "<div class=\"ui warning message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Falha!
		</div>
		<div class=\"content\">
		<ul>".$log."</ul>
		</div>
		</div>";

	} else {
		$celula_log = "<div class=\"ui info message\">
		<i class=\"close icon\"></i>
		<div class=\"header\">
		Atenção!
		</div>
		<div class=\"content\">
		<ul>".$log."</ul>
		</div>
		</div>";

	}

	return $celula_log;
}

// Funï¿½ï¿½o para checar atualizaï¿½ï¿½es do sistema
function checar_versao() {

	$fp = @fsockopen('isistem.com.br', 80, $errno, $errstr, 8);
	if (!$fp) {
		return "não foi possível verificar as atualizações! Tente novamente mais tarde.";
	} else {
		$request = "GET http://www.isistem.com.br/update/versao.atual.php HTTP/1.0\r\n"
		."Host: isistem.com.br\r\n"
		."Connection: close\r\n\r\n";
		fputs($fp, $request);
		do {
			$response = fgets($fp, 1024);
		}
		while (!feof($fp) && !stristr($response, 'Location'));
		fclose($fp);
		return $response;
	}
}
// Funï¿½ï¿½o para criar barra de porcentagem de uso de espaï¿½o e trafego
function barra_uso_plano($usado) {

	if($usado == "") {

		return 'Informação não disponível no momento.';

	} else {

		if($usado > 100) {
			return '<div id="quadro_barra_porcetagem_uso">
			<div id="barra_porcetagem_uso" style="width:100%;">
			<div style="z-index:100;text-align:center;position:absolute;width:150px">'.$usado.'%</div>
			</div>
			</div>';
		} else {
			return '<div id="quadro_barra_porcetagem_uso">
			<div id="barra_porcetagem_uso" style="width:'.$usado.'%;">
			<div style="z-index:100;text-align:center;position:absolute;width:150px">'.$usado.'%</div>
			</div>
			</div>';
		}

	}

}

function forma_pagamento($forma_pagto,$fatura) {

	$getIncludedFiles = get_included_files();
	$temInclude = false;
	foreach ($getIncludedFiles as $filename) {
	    preg_match("/config.php/i", $filename, $matches);
	    if ($matches) {
	        $temInclude = true;
	    }
	}
	if (!$temInclude) {
		include('./inc/config.php');
	}

	$core = new IsistemCore();
	$core->Connect();

	$dados_empresa = $core->Fetch("SELECT * FROM empresa");
	$dados_forma_pagamento = $core->Fetch("SELECT * FROM formas_pagamento where codigo = '".$forma_pagto."'");

	if($dados_forma_pagamento[tipo_pagamento] == 'deposito') {
		return "
		Banco: $dados_forma_pagamento[banco]<br>
		Agência: $dados_forma_pagamento[agencia]<br>
		Conta: $dados_forma_pagamento[conta]<br>
		Tipo: $dados_forma_pagamento[tipo_conta]<br>
		Documento: $dados_forma_pagamento[cpf_cnpj]<br>
		Titular: $dados_forma_pagamento[cedente]<br>
		";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'pagseguro') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão/link abaixo, você será direcionado para o site PagSeguro.com.br para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/pagseguro.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"https://pagseguro.uol.com.br/Security/Imagens/btnPagarBR.jpg\" border=\"0\" alt=\"Pague com PagSeguro - rápido, grátis e seguro!\" /></a>";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'sendep') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site Sendep.com.br para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/sendep.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"http://www.sendep.com.br/buttons/paynow1.gif\" border=\"0\" alt=\"Pagamento Eletrônico Facilitado\" /></a>";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'f2b') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site F2B.com.br para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/f2b.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"".$dados_empresa[url_sistema]."/img/botoes/Botao_F2B.jpg\" border=\"0\" alt=\"Pagamento Eletrônico Facilitado\" /></a>";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'paypal') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site PayPal.com para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/paypal.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"".$dados_empresa[url_sistema]."/img/botoes/Botao_PayPal.jpg\" border=\"0\" alt=\"Pagamento Eletrônico PayPal\" /></a>";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'pagamentodigital') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site PagamentoDigital.com para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/pagamentodigital.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"".$dados_empresa[url_sistema]."/img/botoes/Botao_PagamentoDigital.jpg\" border=\"0\" alt=\"Pagamento Digital\" /></a>";

	} elseif($dados_forma_pagamento[tipo_pagamento] == 'moip') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site MoIP.com para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/moip.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"".$dados_empresa[url_sistema]."/img/botoes/Botao_MoIP.png\" border=\"0\" alt=\"MoIP\" /></a>";

	}
	elseif($dados_forma_pagamento[tipo_pagamento] == 'bcash') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		return "Clique no botão abaixo, você será direcionado para o site bcash.com.br para completar seu pagamento em ambiente seguro.<br><br><a href=\"".$dados_empresa[url_sistema]."/bcash.php?codigo=".$fatura."\" target=\"_blank\"><img src=\"".$dados_empresa[url_sistema]."/img/botoes/bcash.png\" border=\"0\" alt=\"Bcash\" /></a>";

	}
	elseif($dados_forma_pagamento[tipo_pagamento] == 'gerencianet' && $dados_forma_pagamento['gerencia_formas'] == 'boleto') {

// Codifica cï¿½digo da fatura

		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/gr-boleto.php?f=".$fatura."'>
		Visualizar Boleto
		</a></div>";

	}
	elseif($dados_forma_pagamento[tipo_pagamento] == 'gerencianet' && $dados_forma_pagamento['gerencia_formas'] == 'cartao') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/gerencia-card.php?c=".$fatura."'>
		Concluir Pagamento
		</a></div>";

	}
	elseif($dados_forma_pagamento[tipo_pagamento] == 'mercadopago') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/mp-generate.php?f=".$fatura."'>
		Concluir Pagamento
		</a></div>";

	}elseif($dados_forma_pagamento[tipo_pagamento] == 'paghiper') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/ph-view.php?f=".$fatura."'>
		Visualizar Boleto
		</a></div>";

	}elseif($dados_forma_pagamento[tipo_pagamento] == 'bitpay') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/bitpay.php?f=".$fatura."'>
		Pagar com Bitcoin
		</a></div>";

	}elseif($dados_forma_pagamento[tipo_pagamento] == 'boletointer') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/banco_inter.php?f=".$fatura."'>
		Visualizar Boleto
		</a></div>";

	}elseif($dados_forma_pagamento[tipo_pagamento] == 'boletofacil') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/bf.slip.php?f=".$fatura."'>
		Visualizar Boleto
		</a></div>";

	}elseif($dados_forma_pagamento[tipo_pagamento] == 'iugu' && $dados_forma_pagamento[iugu_forma] == 'boleto') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->Fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/iugu.slip.php?f=".$fatura."'>
		Visualizar Boleto
		</a></div>";

	}
	elseif($dados_forma_pagamento[tipo_pagamento] == 'iugu' && $dados_forma_pagamento[iugu_forma] == 'cartao') {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$dados_empresa = $core->fetch("SELECT * from empresa");

		return "<div><a class=\"ui orange button\" href='".$dados_empresa['url_sistema']."/iugu.card.php?f=".$fatura."'>
		Concluir Pagamento
		</a></div>";

	}
	else {

// Codifica cï¿½digo da fatura
		$fatura = encode_decode($fatura,"E");

		$botao_boleto = $dados_empresa[url_sistema]."/img/botoes/".str_replace("php", "jpg", $dados_forma_pagamento['tipo_pagamento']);

		return "<a class=\"ui orange button\" href='".$dados_empresa[url_sistema]."/boleto.php?codigo=".$fatura."' target=\"_blank\">Visualizar Boleto</a> ";
	}


}
// Funï¿½ï¿½o para checar o status do servidor
function status_servidor($servidor,$ip,$porta){
	$randd = rand();
	if(@fsockopen($ip,$porta,$errono,$errstr,5)){
		return '<div class="ui basic segment mzero pzero" data-variation="tiny flowing" data-html="<em><strong>Servidor Online</strong></em>Status: Online(porta 80)"><i class="power large green icon mzero pzero"></i></div>';
	}else{
		return '<div class="ui basic segment mzero pzero" data-variation="tiny flowing" data-html="<em><strong>Falha!</strong><br></em>Status: Falha na Verificação"><i class="power large red icon mzero pzero"></i></div>';
	}
}
// Funï¿½ï¿½o para checar o status do servidor
function status_monitoramento($ip,$porta){
	if(@fsockopen($ip,$porta,$errono,$errstr,5)){
		return "online";
	}else{
		return "offline";
	}
}
// Funï¿½ï¿½o para codificar e decodificar strings
function encode_decode($texto, $tipo = "E") {

	if($tipo == "E") {

		$sesencoded = $texto;
		$num = mt_rand(0,3);
		for($i=1;$i<=$num;$i++)
		{
			$sesencoded = base64_encode($sesencoded);
		}
		$alpha_array =  array('Y','D','U','R','P','S','B','M','A','T','H');
		$sesencoded =
		$sesencoded."+".$alpha_array[$num];
		$sesencoded = base64_encode($sesencoded);
		return $sesencoded;

	}
	else
	{

		$alpha_array =array('Y','D','U','R','P','S','B','M','A','T','H');

		$decoded = base64_decode($texto);

		//list($decoded,$letter) = split("\+",$decoded);
		list($decoded,$letter) = preg_split("/\+/",$decoded);

		for($i=0;$i<count($alpha_array);$i++)
		{
			if($alpha_array[$i] == $letter)
				break;
		}
		for($j=1;$j<=$i;$j++)
		{
			$decoded = base64_decode($decoded);
		}
		return $decoded;

	}
}

// Funï¿½ï¿½o para ler XML
function ler_xml($string,$tag,$ordem) {

	$resultado = explode("<".$tag.">", $string);
	$resultado = explode("</".$tag.">", $resultado[$ordem]);

	return $resultado[0];

}
// Funï¿½ï¿½o para formatar texto(maiï¿½sculo, minï¿½sculo, etc...)
function formatar_texto($texto,$tipo) {

if($tipo == "letra") { // Primeira letra da frase
	$texto_formatado = ucfirst($texto);
} elseif($tipo == "palavra") { // Primeira letra de cada palavra
	$texto_formatado = ucwords($texto);
} elseif($tipo == "maiusculo") { // Tudo maiusculo
	$texto_formatado = strtoupper($texto);
} elseif($tipo == "minusculo") { // Tudo minusculo
	$texto_formatado = strtolower($texto);
}

return $texto_formatado;
}
// Funï¿½ï¿½o para formatar domï¿½nio
function formatar_dominio($nome,$tld,$tipo) {

	$nome_formatado = str_replace("www.","",$nome);
	$nome_formatado = str_replace("http://","",$nome_formatado);
	$nome_formatado = str_replace($tld,"",$nome_formatado);

	if($tipo == "registrar" || $tipo == "subdominio") {
		return $nome_formatado.$tld;
	} else {

		if(substr($tld, 0, 1) == '.') {
			return $nome_formatado.$tld;
		} else {
			return $nome_formatado.".".$tld;
		}

	}
}
// FUnï¿½ï¿½o para converter a periodicidade em nome ou nï¿½mero
function converter_periodicidade($periodicidade,$modo) {

	if($modo == 'numero') {

		if($periodicidade == 'mensal') {
			return "1";
		} elseif($periodicidade == 'bimestral') {
			return "2";
		} elseif($periodicidade == 'trimestral') {
			return "3";
		} elseif($periodicidade == 'semestral') {
			return "6";
		} elseif($periodicidade == 'anual') {
			return "12";
		} elseif($periodicidade == 'bianual') {
			return "24";
		}

	} else {

		if($periodicidade == '1') {
			return "mensal";
		} elseif($periodicidade == '2') {
			return "bimestral";
		} elseif($periodicidade == '3') {
			return "trimestral";
		} elseif($periodicidade == '6') {
			return "semestral";
		} elseif($periodicidade == '12') {
			return "anual";
		} elseif($periodicidade == '24') {
			return "bianual";
		}

	}

}
// Funï¿½ï¿½o para calcular a diferï¿½nï¿½ de dias entre datas
function diferenca_data($Data1, $Data2, $Intervalo) {

	switch($Intervalo){

case 'n' : $Q = 60; break;        //minuto
case 'h' : $Q = 3600; break;      //hora
case 'd' : $Q = 86400; break;    //dia
case 'm' : $Q = 2592000; break;  //mes
case 'a' : $Q = 86400*365; break; //ano
default  : $Q = 1; break;        //segundo
}

return intval ((strtotime($Data2) - strtotime($Data1)) / $Q);
}
// Funï¿½ï¿½o para retornar o nome do campo
function nome_campo($campo) {

	$campo = str_replace("nome","Nome",$campo);
	$campo = str_replace("email1","E-mail Principal",$campo);
	$campo = str_replace("email2","E-mail Secundïário",$campo);
	$campo = str_replace("fone","Telefone",$campo);
	$campo = str_replace("celular","Celular",$campo);
	$campo = str_replace("fax","FAX",$campo);
	$campo = str_replace("cpf","CPF",$campo);
	$campo = str_replace("rg","RG",$campo);
	$campo = str_replace("razao_social","Razão Social",$campo);
	$campo = str_replace("cnpj","CNPJ",$campo);
	$campo = str_replace("endereco","Endereço",$campo);
	$campo = str_replace("numero","Número",$campo);
	$campo = str_replace("bairro","Bairro",$campo);
	$campo = str_replace("cep","CEP",$campo);
	$campo = str_replace("cidade","Cidade",$campo);
	$campo = str_replace("estado","Estado",$campo);

	return $campo;
}

function data_para_bd ($data){

	$data = explode("/", $data);
	$data = "$data[2]-$data[1]-$data[0]";
	return $data;

}

function data_para_usuario ($data){

	$data = explode("-", $data);
	$data = "$data[2]/$data[1]/$data[0]";
	return $data;

}

	//Gera codigo de afiliado

function geraAfiliados($tamanho , $maiusculas , $numeros , $simbolos , $cliente)
{
		// Caracteres de cada tipo
	$lmin = 'abcdefghijklmnopqrstuvwxyz';
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num = '123456789055';
	$dt = date("HisYmd");

		// Variï¿½veis internas
	$retorno = '';
	$caracteres = '';

		// Agrupamos todos os caracteres que poderï¿½o ser utilizados
	$caracteres .= $lmin;
	if ($maiusculas) $caracteres .= $lmai;
	if ($numeros) $caracteres .= $num;
	if ($simbolos) $caracteres .= $simb;

	$caracteres .= $cliente;
	$caracteres .= $dt;
		// Calculamos o total de caracteres possï¿½veis
	$len = strlen($caracteres);

	for ($n = 1; $n <= $tamanho; $n++) {
		// Criamos um nï¿½mero aleatï¿½rio de 1 atï¿½ $len para pegar um dos caracteres
		$rand = mt_rand(1, $len);
		// Concatenamos um dos caracteres na variï¿½vel $retorno
		$retorno .= $caracteres[$rand-1];
	}

	return $retorno;
}


function criptSenha($usuario,$senha){

	$usuario = sha1($usuario);
	$juncao = "$usuario$senha";
	return md5($juncao);

}

function criptSenhaNew($usuario,$senha){



	$juncao = $senha;

	$crypt = new Crypt();
	$crypt->Mode = Crypt::MODE_HEX;
	$crypt->Key  = '!@#$%&*()_+?:';
	$encrypted = $crypt->encrypt($juncao);
	return $encrypted;

}

function ReverseSenha($senha){


	$crypt = new Crypt();
	$crypt->Mode = Crypt::MODE_HEX;
	$crypt->Key  = '!@#$%&*()_+?:';
	$decrypted = $crypt->decrypt($senha);
	return $decrypted;
}


/* Recuperaï¿½ï¿½o de senha admin  */

function cript_complex($string){

	include_once ('cript_adm.php');

	$crypt = new Crypt2;

	$crypt->setKey('2b04172933bdd55f610040c6c935be77');

	$crypt->setComplexTypes(false);

	$crypt->setData($string);

	return $crypt->encrypt();

}

function decript_complex($string){

	include_once ('cript_adm.php');

	$crypt = new Crypt2;

	$crypt->setKey('2b04172933bdd55f610040c6c935be77');

	$crypt->setComplexTypes(false);

	$crypt->setData($string);

	return $crypt->decrypt();

}

/* Recuperaï¿½ï¿½o de senha admin  */

function validAnexo($file){

	$arqType = $file['type'];

	// Lista de tipos de arquivos permitidos
	$tiposPermitidos = array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png');

	  // Verifica o tipo de arquivo enviado
	if (array_search($arqType, $tiposPermitidos) == false) {
		return 0;
	} else {
		return 1;
	}

}

function geraNome(){
	$data = date("YmdHis");
	$rand = rand(1,9);
	return substr(md5("$data$rand"), 0, 8);
}


function  uploadArquivo($file, $caminho, $novonome){

	if (move_uploaded_file($file['anexo']['tmp_name'], $caminho.$novonome)){
		return true;
	}
	else
	{
		return false;
	}

}



?>
