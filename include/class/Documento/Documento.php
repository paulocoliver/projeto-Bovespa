<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Connection/Abstract.php';

class Documento extends Connection{
	
	protected $_documento;
	protected $_colunas;
	protected $_data;
	
	public function __construct(){}
	
	public function getDescricao(){
		
		if(!empty($this->_documento)){
			return $this->_documento['descricao'];
		}
		return '';
	}
	
	public function setValue($key, $valor, $total=0, $valor_ano_anterior=0, $total_ano_anterior=0){
		if(empty($this->_colunas[$key])){
			throw new Exception("Nenhuma coluna encontrada com a chave $key");
		}
		$this->_colunas[$key]['valor'] = $valor;
		$this->_colunas[$key]['total'] = $total;
		$this->_colunas[$key]['valor_ano_anterior'] = $valor_ano_anterior;
		$this->_colunas[$key]['total_ano_anterior'] = $total_ano_anterior;
	}
	
	public function getData(){
		return $this->_data;
	}
	
	public function setData($data){
		if(substr($data,2,1) == '-'){
			$this->_data = substr($data,6,4).'-'.substr($data,3,2).'-'.substr($data, 0,2);
		}else{
			$this->_data = $data;
		}
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
			return $result->fetch_assoc();
			
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
			$sql = "SELECT * FROM coluna WHERE id_documento = $id_documento ORDER BY codigo";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[$row['id_coluna']] = $row;
	            	$response[$row['id_coluna']]['valor'] = '';
	            	$response[$row['id_coluna']]['total'] = '';
	            	$response[$row['id_coluna']]['valor_ano_anterior'] = '';
	            	$response[$row['id_coluna']]['total_ano_anterior'] = '';
	            }
	        }
	        return $response;
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
}