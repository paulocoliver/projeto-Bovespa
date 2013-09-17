<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/simple_html/simple_html_dom.php';
abstract class Base{
	
	protected $_lista;
	protected $_cookie 	= 'ASP.NET_SessionId=34i0whjzzittfn45sye22f45; __utma=121602778.2006470138.1377353516.1377353516.1377353516.1; __utmc=121602778; __utmz=121602778.1377353516.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); CVMWebCookie=SessionKey=98485118-f324-4cca-8532-c1b33d9061ef';
	protected $_domain 	= 'localhost';
	protected $_user 	= 'root';
	protected $_password  = '';
	
	public function __construct(){
		date_default_timezone_set('America/Sao_Paulo');
		ini_set('max_execution_time', 0);
		$this->_lista = array(
			'A','B','C','D','E','F','G','H','I','J','K','L','M',
			'N','O','P','Q','R','S','T','U','V','W','Y','X','Z',
			'0','1','2','3','4','5','6','7','8','9'
		);
	}
	
	public function setCookie($cookie)
	{
		$this->_cookie = $cookie;
	}
	
	public function getLista()
	{
		return $this->_lista;
	}
	
	public function createDLL()
	{
		try{
			$mysqli = new mysqli($this->_domain, $this->_user, $this->_password);
			if (mysqli_connect_errno()) {
			    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
			}
			$mysqli->autocommit(FALSE);
			$response = $mysqli->multi_query("
				CREATE DATABASE IF NOT EXISTS projeto_db;
				USE projeto_db;
				CREATE TABLE IF NOT EXISTS empresa (
					cvm INT(11) NOT NULL,
					nome VARCHAR(100) NOT NULL,
					cnpj VARCHAR(20) NOT NULL,
					count_links int(11) NOT NULL default 0,
					situacao_registro VARCHAR(45) NOT NULL,
					CONSTRAINT pk_empresa PRIMARY KEY(cvm)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				
				CREATE TABLE IF NOT EXISTS link(
					id_link INT(11) NOT NULL auto_increment,
					cvm INT(11) NOT NULL,
					data DATE NOT NULL,
					descricao VARCHAR(200) NOT NULL,
					link VARCHAR(200) NOT NULL,
					CONSTRAINT pk_link PRIMARY KEY(id_link),
					CONSTRAINT fk_empresa_link FOREIGN KEY(cvm) references empresa(cvm)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			");
			
			if(!$response) {
			    throw new Exception("Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error);
			}
			$mysqli->commit();
			$mysqli->close();
			
		}catch(Exception $erro ){
			
			$mysqli->rollback();
			$mysqli->close();
			die($erro->getMessage());
		}
	}
	
	public function getOutput($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Cookie:'.$this->_cookie,
		));
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$output = str_get_html(curl_exec($ch));
		curl_close($ch);
		
		if(empty($output)){
			die("Nenhum registro encontrado");
		}
		return $output;
	}
}