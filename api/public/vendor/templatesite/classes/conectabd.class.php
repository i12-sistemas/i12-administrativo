<?php
class conectabd {
	private $dbname = "";
	private $conn_details;

	private $dbh;
    private $error;
	private $stmt;
	private $Fdebug;
	private $sessaoUsuario;

	function __construct($conn_details) {
       	$this->dbname = $conn_details["db"];
		$this->Fdebug = $conn_details["debug"];
		$this->conn_details = $conn_details;

        // Set DSN
        $dsn = 'mysql:host=' . $this->conn_details['host'] . ';dbname=' . $this->conn_details['db'];
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try{
            $this->dbh = new PDO($dsn,  $this->conn_details['user'], $this-> conn_details['pass'], $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
			if($this->Fdebug){
				echo $this->error;
			}
        }
   }

   function getDatabaseName() {
       	return $this->dbname;
   }

   function error() {
       	return $this->error;
   }

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function commitTransaction(){
    	return $this->dbh->commit();
	}
	public function rollBackTransaction(){
    	return $this->dbh->rollBack();
	}
	public function rowCount(){
    return $this->stmt->rowCount();
	}

	public function execute(){
		try{
		return $this->stmt->execute();
		}catch(PDOException $e){
            $this->error = array(
						"SQLSTATE" => $e->errorInfo[0],
						"msg" => utf8_encode($e->errorInfo[2]),
						"msgcompleta" => utf8_encode($e->getMessage())
					);
        }
	}
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}


   public function single(){
    	$this->execute();
	    return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function resultset(){
    	$this->execute();
	    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

   function DadosUsuarioAtual() {
		self::IniciarSessao();
		$iduser="";
		if(isset($_SESSION["proxy_" . $this->dbname . "user_id"])){
			$iduser = $_SESSION["proxy_" . $this->dbname . "user_id"];
		}
		$nomeusuario="";
		if(isset($_SESSION["proxy_" . $this->dbname . "user_nome"])){
			$nomeusuario = $_SESSION["proxy_" . $this->dbname . "user_nome"];
		}
		$login="";
		if(isset($_SESSION["proxy_" . $this->dbname . "user_login"])){
			$login = $_SESSION["proxy_" . $this->dbname . "user_login"];
		}

		$this->sessaoUsuario = array(
			"iduser"	=> $iduser,
			"nome" 			=> $nomeusuario,
			"login" 		=> $login
		);


       	return $this->sessaoUsuario;
   }

	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}


	public function bind($param, $value, $type = null){
    if (is_null($type)) {
        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
        }
    }
    $this->stmt->bindValue($param, $value, $type);
	}




	private function IniciarSessao(){
		if(!isset($_SESSION)) {
			session_cache_expire(60);
			session_start();
		}
	}

	function EstaAutenticado(){
		self::IniciarSessao();
		try{
			$autenticado = "N";
			$varsession_auth = "proxy_" . $this->dbname . "autenticado";
			if(isset($_SESSION[$varsession_auth])){
				$autenticado = $_SESSION[$varsession_auth];
			}
			return $autenticado;
		} catch (Exception $e) {
			return "N";
		}
	}

	public function DestroiSessao(){
		self::IniciarSessao();

		if(isset($_SESSION["proxy_" . $this->dbname . "user_id"])){
			unset($_SESSION["proxy_" . $this->dbname . "user_id"]);
		}

		if(isset($_SESSION["proxy_" . $this->dbname . "user_nome"])){
			unset($_SESSION["proxy_" . $this->dbname . "user_nome"]);
		}

		if(isset($_SESSION["proxy_" . $this->dbname . "user_login"])){
			unset($_SESSION["proxy_" . $this->dbname . "user_login"]);
		}
		if(isset($_SESSION["proxy_" . $this->dbname . "autenticado"])){
			unset($_SESSION["proxy_" . $this->dbname . "autenticado"]);
		}
		setcookie("proxy_" . $this->dbname . "_lastpageaccess","", time()-3600);
		unset ($_COOKIE["proxy_" . $this->dbname . "_lastpageaccess"]);
	}






	function fatal ( $msg ) {
		echo json_encode( array(
			"error" => $msg
		) );

		exit(0);
	}
}
