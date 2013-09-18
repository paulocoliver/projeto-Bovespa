<?php
include_once '/../Connection/Abstract.php';

class Auth_Usuario extends Connection{
	
	private $_usuario;
	
	public function __construct(){
		@session_start();
		if(isset($_SESSION['usuario'])&&!empty($_SESSION['usuario'])){
			$this->_usuario =  $_SESSION['usuario'];
		}
	}
	
	public function isAuthenticated(){
		return empty($this->_usuario) ? false : true;
	}
	
	public function getUsuario($field){
		if($field&&$this->_usuario[$field]){
			return $this->_usuario[$field];
		}
		return $this->_usuario;
	}
	
	public function login($email,$senha){
		
		$senha = str_replace(array('\\','/',"'",'"'),'',$senha);
		$this->Connect();
		$sql = "
			SELECT * FROM usuario_empresa 
			WHERE email = '$email' AND senha = '$senha'
		";
		$result = $this->_mysqli->query($sql);
        
		if(!$result->num_rows){
			throw new Exception('Usuário ou senha incorretos');
		}else{
			$_SESSION['usuario'] = $result->fetch_assoc();
			$this->redirect();
		}
	}
	
	public function cadastrar($razao_social,$cnpj,$email,$senha){
		
		$this->Connect();
		
		$sql = "
			INSERT INTO usuario_empresa 
			(razao_social,cnpj,email,senha)
			VALUES
			('$razao_social','$cnpj','$email','$senha')
		";
		
		$this->_mysqli->query($sql);
		if (mysqli_connect_errno()) {
		    throw new Exception(mysqli_connect_error());
		}
		$this->login($email, $senha);
	}
	
	public function redirect()
	{
		header("location:/usuario/index.php");
	}
}