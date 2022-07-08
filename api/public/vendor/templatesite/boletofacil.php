<?php
$cnpj = '';
if(isset($_GET["cnpj"])){
  $cnpj=$_GET["cnpj"];
}


$fatura = '';
if(isset($_GET["fatura"])){
  $fatura=$_GET["fatura"];
}

$token = '';
if(isset($_GET["token"])){
  $token=$_GET["token"];
}

$url = 'http://ws.idoze/api/boleto?cnpj=' . $cnpj . '&fatura=' . $fatura . '&token=' . $token;

$res = file_get_contents($url);
$res = utf8_encode($res);
$result = json_decode($res, true);
if (count($result)<=0){
    throw new Exception('Falha ao checar dados.');
}
$row = $result["rows"];
$boleto = base64_decode( $row["file"] );
$finfo = new finfo(FILEINFO_MIME);
$mimetype = $finfo->buffer($boleto);
// var_dump($boleto);
// var_dump($mimetype);


$file =  $row["filename"];
file_put_contents($file, $boleto);

if (file_exists($file)) {
    header('Content-Description: Boleto i12 Sistemas - www.idoze.com.br');
    header('Content-Type: ' . $mimetype);
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    echo $boleto;
    exit;
}


?>
