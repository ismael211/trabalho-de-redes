<?php

//////////////////////////////////////////////////////////////////////////
// Isistem Sistema Financeiro para Host's	   		                    //
// Descricao: Sistema de Gerenciamento de Clientes		                //
// Isistem Sistemas Web		                                            //
// Site: www.Isistem.com.br       										//
//////////////////////////////////////////////////////////////////////////


class IsistemCore
{
	private $PDO;
	function __construct()
	{
		# code...
	}

	function Connect(){
		define( 'MYSQL_HOST', 'localhost' );
		define( 'MYSQL_USER', 'negoplay_negoplay' );
		define( 'MYSQL_PASSWORD', 'wPHofwg!VyBJ' );
		define( 'MYSQL_DB_NAME', 'negoplay_isistem' );

		try{
		    $this->PDO = new PDO( 'mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
		    $this->PDO->exec("SET character_set_results = 'ISO-8859-1'");
		    $this->PDO->exec("SET names = 'ISO-8859-1'");
		    $this->PDO->exec("SET SQL_MODE='ALLOW_INVALID_DATES';");
		    //$this->PDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch ( PDOException $e ){
		    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
		}
	}

	function Query($sql){
		return $this->PDO->query($sql);
	}

	function Exec($sql){
		return $this->PDO->exec($sql);
	}
	function LastId(){
		return $this->PDO->lastInsertId();
	}
	function BeginT(){
		return $this->PDO->beginTransaction();
	}
	function Rollback(){
		return $this->PDO->rollBack();
	}
	function Commit(){
		return $this->PDO->commit();
	}

	function Fetch($sql){
		$result = $this->Query($sql);
		if ($result) {
			return $result->fetch(PDO::FETCH_ASSOC);
		}else{
			return false;
		}
	}

	function FetchAll($sql){
		$result = $this->Query($sql);
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	function RowCount($sql){
		$result = $this->Query($sql);
		return $result->rowCount();
	}

	function Prepare($sql){
		return $this->PDO->prepare($sql);
	}

	function Execute($params = null){
		return $this->PDO->execute($params);
	}

	function ErrorInfo(){
		$message = $this->PDO->errorInfo();
		return $message;
	}

}
