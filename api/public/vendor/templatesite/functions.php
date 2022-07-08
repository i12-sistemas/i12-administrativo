<?php
set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

function GetStatusChati12(){
	// returns a single line of JSON that contains the video title. Not a giant request.
	$online = false;
	$msgErro = '';
	try{
		$ret = file_get_contents("http://idoze.mysuite1.com.br/empresas/idz/verificanum.php");
		$online = $ret!='1' ? false : true;
	}
	catch (Exception $e) {
		$msgErro=$e->getMessage();
	}
	//echo var_dump($videoTitle);
	$ret2 = array('online' => $online, 'msgErro' => utf8_encode($msgErro));
	return $ret2;
}

function SomenteNumero($s){
	return  preg_replace('#[^0-9]#','',strip_tags($s));
}

function isBoolean($value) {
   return $value === "true";
}

function ValidaDataMySQL($dat){
	try{

		$data = explode("-","$dat"); // fatia a string $dat em pedados, usando / como refer�ncia
		If(count($data)<>3){
			return false;
			exit;
		}
		$y = $data[0];
		$m = $data[1];
		$d = $data[2];

		return checkdate($m,$d,$y);
	} catch (Exception $e) {
		return false;
	}
}

function ValidaData($dat){
	try{
		$data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como refer�ncia
		If(count($data)<>3){
			return false;
			exit;
		}
		$d = $data[0];
		$m = $data[1];
		$y = $data[2];
		return checkdate($m,$d,$y);
	} catch (Exception $e) {
		return false;
	}
}

function validaCPF($cpf = null) {

	// Verifica se um n??o foi informado
	if(empty($cpf)) {
		return false;
	}

	// Elimina possivel mascara
	$cpf = ereg_replace('[^0-9]', '', $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	// Verifica se o numero de digitos informados ?gual a 11
	if (strlen($cpf) != 11) {
		return false;
	}
	// Verifica se nenhuma das sequ?ias invalidas abaixo
	// foi digitada. Caso afirmativo, retorna falso
	else if ($cpf == '00000000000' ||
			$cpf == '11111111111' ||
			$cpf == '22222222222' ||
			$cpf == '33333333333' ||
			$cpf == '44444444444' ||
			$cpf == '55555555555' ||
			$cpf == '66666666666' ||
			$cpf == '77777777777' ||
			$cpf == '88888888888' ||
			$cpf == '99999999999') {
				return false;
				// Calcula os digitos verificadores para verificar se o
				// CPF ??do
			} else {

				for ($t = 9; $t < 11; $t++) {

					for ($d = 0, $c = 0; $c < $t; $c++) {
						$d += $cpf{$c} * (($t + 1) - $c);
					}
					$d = ((10 * $d) % 11) % 10;
					if ($cpf{$c} != $d) {
						return false;
					}
				}

				return true;
			}
}


function substrwords($text, $maxchar, $end='...') {
	$qtdeChar = strlen($text);
	$texto = $text;
	if($qtdeChar >  $maxchar){
		$texto = substr($text,0, $maxchar)	. $end;
	}
    return $texto;
}


?>
