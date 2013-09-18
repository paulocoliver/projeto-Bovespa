<?php
include_once '/../Connection/Abstract.php';

abstract class Documento extends Connection{
	
	protected $_documento;
	protected $_colunas;
	
	public function __construct(){}
	
	public function setValue($key,$valor){
		if(empty($this->_colunas[$key])){
			throw new Exception("Nenhuma coluna encontrada com a chave $key");
		}
		$this->_colunas[$key]['valor'] = $valor;
	}
	
	public function newDoc($id_documento){
		$this->_documento = $this->getDocumento($id_documento);
		$this->_colunas   = $this->getDocCols($id_documento);
	}
	
	public function getDocumento($id_documento){
		try{
			$this->Connect();
			$sql = "SELECT * FROM documento WHERE id_documento = '$id_documento'";
			$result = $this->_mysqli->query($sql);
        
			if(!$result->num_rows){
				throw new Exception('Documento não encontrado');
			}
			return $result->fetch_object();
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	public function getColunas(){
		return $this->_colunas;
	}
	
	public function getDocCols($id_documento){
		try{
			$this->Connect();
			$sql = "SELECT * FROM coluna WHERE id_documento = $id_documento";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[$row['id_coluna']] = $row;
	            	$response[$row['id_coluna']]['valor'] = '';
	            }
	        }
	        if(!count($response)){
	        	throw new Exception("Não há registro de colunas para o documento.");
	        }
	        return $response;
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
}