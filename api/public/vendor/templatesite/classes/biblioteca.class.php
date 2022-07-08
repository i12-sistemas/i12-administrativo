<?php
// REMOVE THIS BLOCK - used for DataTables test environment only!
$pathroot = $_SERVER['DOCUMENT_ROOT'] . "/";

include_once($pathroot . "inc-conexaobd.php");
include_once($pathroot . "functions.php");

class biblioteca {
	private $teste = "";
	private $connBD;
	 
	function __construct($connBD) {
		$this->connBD = $connBD;
	}
   
	function BACKUP_HORAS_ALERTA() {
		$this->connBD->query("select valor from configuracao where flagid='BACKUP_HORAS_ALERTA'");
		$p = $this->connBD->single();
       	return intval($p["valor"]);
   }
   
	function first_name($nomecompleto) {
		$nomecompleto  = trim($nomecompleto);
		$nomes = explode(" ", $nomecompleto);
		$firstname = "";
		if(count($nomes)>=1){
			$firstname = trim($nomes[0]);
		}

       	return $firstname;
   	}
   
   function config($flagID) {
		$this->connBD->query("SELECT tipocampo, valor, texto FROM sapv.configuracao where flagid='$flagID'");
		$configuracao = $this->connBD->single();
		$tipo = "";
		$valor = "";
		
		if($configuracao){
			$tipo = $configuracao["tipocampo"];
			if($tipo=="MOEDA"){
				try{
					$valor = $configuracao["valor"];
					$valor = str_replace(".", "", $valor);
					$valor = str_replace(",", ".", $valor);
					$valor = str_replace(" ", "", $valor);
					$valor = floatval($valor);
				}catch (Exception $e) {
					$valor = "";
				};
			};
		};
		
		return array("tipo" => $tipo,
					 "valor" => $valor
					 );
   }
   
   
   
   
}

