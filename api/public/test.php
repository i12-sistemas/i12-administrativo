<?php 
echo "iniciado" ;

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'i12@dbcliente';
$dbname = 'cleberinfome';
$senhazip = '@bencoaBKP';

$data = date('Y-m-d-H-i-s');
$path = '/backup/'.$dbname . '/' . $data .  '/ExportBD';
$pathbkp = '/backup/'.$dbname . '/' . $data . '/';
$pathzip = '/backup/' . $dbname . '/';
$filename = $path . '/' .$dbname;
$zipfilename = $pathzip . $data . "-DB-" . $dbname . ".zip";

echo "Delete path $path";
exec("rm -fr  $path");
exec("rm -fr  $pathbkp");
echo "Criando path $path";
exec("mkdir -p ".$path);
echo "Executando backup...";
exec("mysqldump --user=$dbuser --password='$dbpass' --host=$dbhost $dbname --default-character-set=utf8 --routines --skip-triggers --no-data --hex-blob --no-create-info --no-create-db --skip-opt > " . $filename . "_ROUTINES.bkp");
//exec("mysqldump --user=$dbuser --password='$dbpass' --host=$dbhost $dbname --single-transaction --hex-blob --skip-extended-insert --max_allowed_packet=32M  --default-character-set=utf8 --triggers --routines --opt > " . $filename . "_TABLES.bkp");
echo "cd $pathbkp <br>";
chdir($pathbkp);
echo "Compactando...";
exec("7za a -p$senhazip -tzip $zipfilename -ai" . $pathbkp);
echo "Limpando temp\n";
exec("rm -fr  $pathbkp");


$FTPservidor = 'ftp.idoze.com.br';
$FTPporta = 21;
$FTPsenha = 'superbackupi12';
$FTPusuario = 'i12bkpftp';
$FTPpassive = false;

// set up basic connection
$conn_id = ftp_connect($FTPservidor);

// login with username and password
$login_result = ftp_login($conn_id, $FTPusuario, $FTPsenha);

ftp_pasv($conn_id, true);

// upload a file
$fileremote = $data . "-DB-" . $dbname . ".zip";
// echo "\n\n\ $fileremote \n\n\n";
ftp_chdir($conn_id, "./22536307000144/");
echo "\n\n Dire atual:  " . ftp_pwd($conn_id);

// $lista = ftp_nlist($conn_id, "."); 
// echo "\n\n------------------------------------------"; 
// foreach($lista as $list){
// echo "\n<br>--------  ";
// echo json_encode($list);
// };
// flush ; 

if (ftp_put($conn_id, $fileremote, $zipfilename, FTP_BINARY)) {
 echo "successfully uploaded $fileremote\n";
} else {
 print_r(error_get_last());
 echo "There was a problem while uploading $zipfilename\n";
}

// close the connection 
ftp_close($conn_id);
echo "fim" 
?>

