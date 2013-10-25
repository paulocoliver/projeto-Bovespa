<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Connection/Abstract.php';

class Segmento extends Connection{
	
	public function __construct(){}
	
	public function select(){
		
		$this->Connect();
		$sql  = "SELECT * FROM segmento";
		$sql .= " ORDER BY descricao";
		
		$result 	= $this->_mysqli->multi_query($sql);
		$response 	= array();
		
		if($result = $this->_mysqli->use_result()){
			while($row = $result->fetch_assoc()){
				$response[] = $row;
			}
		}
		return $response;
	}
	
	public function selectEmpresa($id_segmento){

		$this->Connect();
		$sql  = "SELECT * FROM empresa e JOIN empresa_segmento es ON es.cvm = e.cvm WHERE es.id_segmento = $id_segmento ORDER BY nome";
		
		$result 	= $this->_mysqli->multi_query($sql);
		$response 	= array();
		
		if($result = $this->_mysqli->use_result()){
			while($row = $result->fetch_assoc()){
				$response[] = $row;
			}
		}
		return $response;
	}
	
	public function mediaEmpresas( Documento $doc, $id_empresas = array() ){
		
		if(!count($id_empresas)){
			throw new Exception('Processo cancelado! Nenhuma empresa deste segmento foi selecionada.');
		}
		$this->Connect();
		
		$id_empresas = implode(',',$id_empresas);
		
		$data = $doc->getData();
		$colunas = $doc->getColunas();
		
		foreach($colunas AS $key => $val ){
			$sql = "SELECT 
						AVG(v.valor) AS valor, 
						AVG(v.total) AS total, 
						AVG(v.valor_ano_anterior) AS valor_ano_anterior, 
						AVG(v.total_ano_anterior) AS total_ano_anterior 
					FROM documento_empresa_valor v 
					JOIN documento_empresa d ON d.id_documento_empresa = v.id_documento_empresa 
					WHERE d.cvm IN ($id_empresas) AND d.data = '$data' AND v.id_coluna = $key;
			";
			$result = $this->_mysqli->multi_query($sql);
		 	
			if($result = $this->_mysqli->use_result()){
				$result = $result->fetch_assoc();
		        $doc->setValue(
		        	$key, 
		        	$result['valor'],
		        	$result['total'],
		        	$result['valor_ano_anterior'],
		        	$result['total_ano_anterior']
		        );
	        }
		}
		
		return $doc;
	}
}