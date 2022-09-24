<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1",true);
//////////////////////////////////////////////////////////////////////////
// Isistem Gerenciador Financeiro para Hosts  		                    //
// Descrição: Sistema de Gerenciamento de Clientes		                //
// Site: www.isistem.com.br       										//
//////////////////////////////////////////////////////////////////////////

require_once('config.php');
require_once('class.whois.php');
require_once('class.mail.php');

$core = new IsistemCore();
$core->Connect();

error_reporting(0);
ini_set("display_errors", "Off");
// Banco de Funções em PHP da assinatura

// Uso global
$dados_empresa = $core->Fetch("SELECT * FROM empresa");
$dados_sistema = $core->Fetch("SELECT * FROM sistema");

// Função para pesquisar dados do plano
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_plano" && !empty($_GET[codigo])){
	if ($_SESSION['tipo_plano_editar'] == 'cloud') {
		$plano_tipo = 'planos_cloud';
	}
	elseif ($_SESSION['tipo_plano_editar'] == 'cvmware') {
		$plano_tipo = 'planos_vmware';
	}
	elseif ($_SESSION['tipo_plano_editar'] == 'teamspeak') {
		$plano_tipo = 'planos_teamspeak';
	}else{
		$plano_tipo = 'planos';
	}

	$dados_plano = $core->Fetch("SELECT * FROM ".$plano_tipo." where codigo = '".$_GET[codigo]."'");
	if($dados_plano[valor_setup] == '0') {
	echo $dados_plano[valor]."&0,00";
	} else {
	echo $dados_plano[valor]."&".$dados_plano[valor_setup];
	}
	exit();
}

// Função para pesquisar dados do serviço
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_servico" && !empty($_GET[codigo])){
	$dados_servico = $core->Fetch("SELECT * FROM servicos_modelos where codigo = '".$_GET[codigo]."'");

	echo $dados_servico[valor];

	exit();
}
// Função para pesquisar o desconto da periodicidade
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_periodicidade" && !empty($_GET[p])){
	$dados_periodicidade = $core->Fetch("SELECT * FROM empresa");

	echo $dados_periodicidade["desconto_".$_GET[p].""];

	exit();
}

// Função para retornar o tipo de um servidor
if(isset($_POST[acao]) && $_POST[acao] == "get_server_type" && !empty($_POST[c])){
	$dados_server = $core->Fetch("select * from servidores where codigo=".$_POST[c]);

	echo $dados_server[tipo];

	exit();
}

// Função para retornar os planos de um servidor plesk
if(isset($_POST[acao]) && $_POST[acao] == "get_plesk_plans" && !empty($_POST[c])){
	require("Plesk.API.php");
	$dados_servidor = $core->Fetch("select * from servidores where codigo = ".$_POST[c]);

	$plesk = new PleskAPI($dados_servidor[ip], $dados_servidor[plesk_usuario], $dados_servidor[plesk_senha]);

	if ($_POST[tipo] == 'h') {
		$result = $plesk->plesk_service_plans();

		$array_xmls = array();
		foreach ($result as $item) {
			$array_xmls[] = simplexml_load_string($item);
		}

		$array_result = array();

		foreach ($array_xmls as $service_plan) {
			//var_dump($service_plan->{'service-plan'}->{'get'}->{'result'}->{'name'});
			if (isset($service_plan->{'service-plan'}->{'get'}->{'result'})) {
				foreach ($service_plan->{'service-plan'}->{'get'}->{'result'} as $service) {
					array_push($array_result, array('id' => $service->{'id'}."", 'name'=> $service->{'name'}.""));
				}
			}
		}
	}elseif ($_POST[tipo] == 'r') {
		$result = $plesk->plesk_reseller_plans();
		$array_result = array();
		foreach ($result as $reseller) {
			array_push($array_result, array('id' => str_replace(" ", "/=/", $reseller[name])."", 'name'=> $reseller[name].""));
		}
	}else{ // caso o tipo selecionado seja revenda

	}
	header('Content-Type: application/json');
	echo json_encode($array_result);

	exit();
}

// Função para pesquisar as periodicidades do planos
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_periodicidade_plano" && !empty($_GET[codigo_plano])){

	$periodicidades_sistema = explode(",",$dados_empresa['periodicidades']);

	if ($_SESSION['tipo_plano_editar'] == 'cloud') {
		$plano_tipo = 'planos_cloud';
	}elseif ($_SESSION['tipo_plano_editar'] == 'teamspeak') {
		$plano_tipo = 'planos_teamspeak';
	}else{
		$plano_tipo = 'planos';
	}

	$dados_plano = $core->Fetch("SELECT * FROM $plano_tipo where codigo = '".$_GET[codigo_plano]."'");

	if($dados_plano[periodicidade_minima] == 'mensal') {
	$periodicidades_disponiveis = "mensal,bimestral,trimestral,semestral,anual,bianual";
	} elseif($dados_plano[periodicidade_minima] == 'bimestral') {
	$periodicidades_disponiveis = "bimestral,trimestral,semestral,anual,bianual";
	} elseif($dados_plano[periodicidade_minima] == 'trimestral') {
	$periodicidades_disponiveis = "trimestral,semestral,anual,bianual";
	} elseif($dados_plano[periodicidade_minima] == 'semestral') {
	$periodicidades_disponiveis = "semestral,anual,bianual";
	} elseif($dados_plano[periodicidade_minima] == 'anual') {
	$periodicidades_disponiveis = "anual,bianual";
	} elseif($dados_plano[periodicidade_minima] == 'bianual') {
	$periodicidades_disponiveis = "bianual";
	}

	foreach($periodicidades_sistema as $periodicidade){

	if(preg_match("/\b".$periodicidade."\b/i",$periodicidades_disponiveis)) {
	$lista_periodicidades .= $periodicidade."|";
	}

	}

	echo substr($lista_periodicidades, 0, -1);

	exit();
}

// Função para validar cupom de desconto para assinatura geral
if(isset($_GET[acao]) && $_GET[acao] == "validar_cupom" && !empty($_GET[codigo])){

	$dados_cupom = $core->Fetch("SELECT * FROM cupons where cupom = '".$_GET[codigo]."'");
	$total_cupom = $core->RowCount("SELECT * FROM cupons where cupom = '".$_GET[codigo]."'");

	if($dados_cupom[area] != 'g' && $dados_cupom[area] != 't') {
		echo "erro4&";
		exit();
	}

	if($dados_cupom[data_expiracao] < date("Y-m-d") && $dados_cupom[data_expiracao] != '0000-00-00') {
		echo "erro1&";
		exit();
	}

	if($dados_cupom[periodicidades ] != $_SESSION['periodicidades'] && $dados_cupom[periodicidades ] != 'todas') {
		echo "erro2&";
		exit();
	}

	if($total_cupom == 0) {

		echo "erro3&";
		$_SESSION['cupom_valor'] = 0;
		exit();

	} else {

		$planotipo = ($_SESSION['t']=='c') ? 'c' : 'h' ;


		// Verifica se o cupom é válido para o plano escolhido
		$planos_cupom = explode(",", $dados_cupom[planos]);
		$plano0 = $planotipo.$_SESSION['plano'];
		if(in_array($plano0, $planos_cupom)) {

		// Atualiza a quantidade de uso do cupom
		$query = $core->Prepare("UPDATE cupons SET uso=uso+1 WHERE cupom = '".$_GET[codigo]."'");
		$result = $query->Execute();

			if($dados_cupom[tipo] == 'p') {
				$valor_desconto = $_SESSION['valor_primeiro_mes'];
				$valor_desconto = $valor_desconto/100;
				$_SESSION['cupom_valor'] = $valor_desconto*$dados_cupom[valor];
			} else {
				$_SESSION['cupom_valor'] = $dados_cupom[valor];
			}

			if($dados_cupom[periodo] == '1') {

				$_SESSION['valor_primeiro_mes_cupom'] = $_SESSION['valor_primeiro_mes']+$_SESSION['div_valor_servicos']-$_SESSION['cupom_valor'];
				$_SESSION['valor_demais_meses_cupom'] = $_SESSION['valor_demais_meses']+$_SESSION['div_valor_servicos'];

			} else {

				$_SESSION['valor_primeiro_mes_cupom'] = $_SESSION['valor_primeiro_mes']+$_SESSION['div_valor_servicos']-$_SESSION['cupom_valor'];
				$_SESSION['valor_demais_meses_cupom'] = $_SESSION['valor_demais_meses']+$_SESSION['div_valor_servicos']-$_SESSION['cupom_valor'];

			}

		echo "ok&".number_format($_SESSION['cupom_valor'],2,",",".")."&".number_format($_SESSION['valor_primeiro_mes_cupom'],2,",",".")."&".number_format($_SESSION['valor_demais_meses_cupom'],2,",",".")."&".$dados_cupom[nome]."&".$dados_cupom[periodo];

		$_SESSION['cupom'] = $_GET[codigo];

		exit();
		}
		else {
			echo "erro4&";
			exit();
		}
	}

	exit();

}





// Função para validar cupom de desconto para assinatura de domínios
if(isset($_GET[acao]) && $_GET[acao] == "validar_cupom_dominios" && !empty($_GET[codigo])){
	$dados_cupom = $core->Fetch("SELECT * FROM cupons where cupom = '".$_GET[codigo]."'");
	$total_cupom = $core->RowCount("SELECT * FROM cupons where cupom = '".$_GET[codigo]."'");

	if($dados_cupom[area] != 'd' && $dados_cupom[area] != 't') {
	echo "erro1&";
	exit();
	}

	if($dados_cupom[data_expiracao] < date("Y-m-d") && $dados_cupom[data_expiracao] != '0000-00-00') {
	echo "erro2&";
	exit();
	}

	if($total_cupom == 0) {
	echo "erro3&";
	$_SESSION['cupom_valor'] = 0;
	exit();
	} else {

	$query = $core->Prepare("Update cupons set uso=uso+1 where cupom = '".$_GET[codigo]."'");
	$result = $query->Execute();

	if($dados_cupom[tipo] == 'p') {
	$valor_desconto = $_SESSION['valor_registro_dominio'];
	$valor_desconto = $valor_desconto/100;
	$valor_desconto = $valor_desconto*$dados_cupom[valor];
	} else {
	$valor_desconto = $dados_cupom[valor];
	}

	$valor_faturas = $_SESSION['valor_registro_dominio']-$valor_desconto;

	echo "ok&".number_format($valor_desconto,2,",",".")."&".number_format($valor_faturas,2,",",".")."&".$dados_cupom[nome];

	$_SESSION['valor_registro_dominio'] = $valor_faturas;
	$_SESSION['cupom_valor'] = $valor_desconto;
	$_SESSION['cupom'] = $_GET[codigo];

	exit();
	}

}
// Função para pesquisar valor do registro de domínio pela extensão
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_dominio_valor" && !empty($_GET[extensao])){
	$dados_extensao = $core->Fetch("SELECT * FROM precos_dominios where extensao = '".$_GET[extensao]."'");

	echo $dados_extensao[valor]."&".number_format($dados_extensao[valor],2,",",".");

	exit();
}
// Função para formatar moeda em R$
if(isset($_GET[acao]) && $_GET[acao] == "number_format" && $_GET[valor] != ""){

	echo number_format($_GET[valor],2,",",".");

	exit();
}

// Função para validar cliente
if(isset($_GET[acao]) && $_GET[acao] == "validar_usuario" && !empty($_GET[usuario]) && !empty($_GET[senha])){

	require_once("Sanatize.php");
	require_once("funcoes.php");

	$cliente_email =  Sanitize::filter($_GET[usuario]);
	$cliente_senha =  Sanitize::filter($_GET[senha]);
$cliente_senha = base64_decode($cliente_senha);
$checar_cliente = $core->Fetch("SELECT * FROM clientes WHERE email1 = '".$cliente_email."' LIMIT 1");


if($checar_cliente['tipo_senha'] == 'md5'){
	$senha = criptSenha($cliente_email,$cliente_senha);

}else{
	$senha = criptSenhaNew($cliente_email,$cliente_senha);
	$real = ReverseSenha($checar_cliente['senha']);

}



	$resultado = $core->RowCount("SELECT * FROM clientes WHERE email1 = '".$cliente_email."' AND senha = '".$senha."' LIMIT 1");

	if($resultado == 0 || $resultado == '') {
		echo "erro";
	} else {

		$pessoa = $core->Fetch("select * from clientes where email1 = '$cliente_email'");
		$_SESSION['cliente_logado'] = $cliente_email;
		$_SESSION['cliente_pessoa'] = $pessoa['tipo_pessoa'];
		echo "ok";
	}

	exit();
}
// Função para pesquisar valor do dominio
if(isset($_GET[acao]) && $_GET[acao] == "pesquisar_extensao_valor" && !empty($_GET[codigo])){
	$dados_extensao = $core->Fetch("SELECT * FROM precos_dominios where codigo = '".$_GET[codigo]."'");

	echo $dados_extensao[valor];

	exit();
}

if(isset($_GET[acao]) && $_GET[acao] == 'pesquisar_dominio_whois' && !empty($_GET[dominio])){

	$dominio =  $_GET[dominio];

	if ($dominio != "") {

	    if ($dominio == "") {
	        echo "<script src=\"../alert/sweetalert.min.js\"></script> <link rel=\"stylesheet\" type=\"text/css\" href=\"../alert/sweetalert.css\"><script>swal({   title: \"Atenção\",   text: \" <strong>Atenção</strong> - Domínio em branco. \", html: true , type: \"error\",   confirmButtonText: \"ok\" });</script>";
	    }
	    else {

	        require 'whois/Whois.php';

	        $sld = $dominio;
	        $domain = new Whois($sld);

	        $whois_answer = $domain->info();

	        //echo $whois_answer;

	        if ($domain->isAvailable()) {
	            echo "0";
	        } else {

	            echo "1";
	        }
	    }

	}
}
?>
