<?php
abstract class Connection{
	
	protected $_mysqli;
	protected $_domain 	= 'localhost';
	protected $_user 	= 'root';
	protected $_password  = 'Paul0405';
	
	public function __construct(){}
	
	public function Connect(){
		$this->_mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
	}
	
	public function createDLL(){
		$ddl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/include/class/ddl.txt');
		try{
			$this->connect();
			$this->_mysqli->autocommit(FALSE);
			$response = $this->_mysqli->multi_query($ddl);
			if(!$response) {
			    throw new Exception("Multi query failed: (" . $this->_mysqli->errno . ") " . $this->_mysqli->error);
			}
			$this->_mysqli->commit();
			$this->_mysqli->close();
			
		}catch(Exception $erro ){			
			$this->_mysqli->rollback();
			$this->_mysqli->close();
			die($erro->getMessage());
		}
	}
}