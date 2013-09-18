<?php
include_once 'Documento.php';

class DocumentoBovespa extends Documento{
	
	private $_cvm;
	
	public function __construct($cvm){
		$this->_cvm = $cvm;
	}
	
	public function select($id_documento_empresa=null){
		try{
			$sql   = "
					SELECT 
						de.id_documento_empresa, 
						de.id_documento,
						DATE_FORMAT(de.data,'%d-%m-%Y') AS data,
						d.descricao FROM documento_empresa de 
					LEFT JOIN documento d on d.id_documento = de.id_documento
					WHERE de.cvm = {$this->_cvm}
				";
			
			if(!empty($id_documento_empresa)){
				$sql.= " AND de.id_documento_empresa = $id_documento_empresa";
			}
			$sql.= " ORDER by de.data";
			
			$this->Connect();
			
			$response = array();
			$result = $this->_mysqli->multi_query($sql);
		 	
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[] = $row;
	            }
	        }
	        return $response;
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	public function selectByLink(){
		try{
			$this->Connect();
			$result = $this->_mysqli->query("SELECT id_documento_empresa FROM documento_empresa WHERE cvm = {$this->_cvm} AND data = '{$this->_data}'");
		
			if(!$result->num_rows){
				return null;
			}
			$res = $result->fetch_assoc();
			return $res['id_documento_empresa'];
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	public function loadDoc($id_documento_empresa){
		$doc = $this->select($id_documento_empresa);
		$this->_documento = $doc[0];
		$this->setData($this->_documento['data']);
		$this->_colunas = $this->getDocCols($this->_documento['id_documento']);
		
		$this->loadValues($id_documento_empresa);
	}
	
	public function loadValues($id_documento_empresa){
		try{
			$this->Connect();
			$sql = "
					SELECT id_coluna, valor FROM documento_empresa_valor 
					WHERE id_documento_empresa = $id_documento_empresa 
			";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$this->setValue($row['id_coluna'], $row['valor']);
	            }
	        }
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	public function inserir(){
		try{
			$this->Connect();
			$this->_mysqli->autocommit(FALSE);
			$insert = false;
			
			if(!isset($this->_documento['id_documento_empresa'])){
				$insert = true;
				$sql = "
					INSERT INTO documento_empresa
					(cvm,id_documento,data)
					values
					({$this->_cvm},{$this->_documento['id_documento']},'{$this->_data}')
				";
				
				$this->_mysqli->query($sql);
				$id_documento_empresa = $this->_mysqli->insert_id;
				
			}else{
				$id_documento_empresa = $this->_documento['id_documento_empresa'];
			}
			
			foreach($this->_colunas AS $coluna):
				$valor = !empty($coluna['valor']) ? str_replace(',','.',str_replace('.','',$coluna['valor'])) : 0; 
				
				if($insert){
					$sql = "
						INSERT INTO documento_empresa_valor
						(id_documento_empresa,id_coluna,valor)
						values
						($id_documento_empresa,{$coluna['id_coluna']},$valor);
					";
				}else{
					$sql = "
						UPDATE documento_empresa_valor set valor = $valor 
						WHERE id_coluna = {$coluna['id_coluna']} AND id_documento_empresa = $id_documento_empresa
					";
				}
				$this->_mysqli->query($sql);
			endforeach;
			
			$this->_mysqli->commit();
			$this->_mysqli->close();
			
		}catch(Exception $erro ){
			
			$this->_mysqli->rollback();
			$this->_mysqli->close();
			
			die($erro->getMessage());
		}
	}
	
	public function deletar($id_documento_empresa){
		$this->_mysqli->query("DELETE FROM documento_empresa WHERE id_documento_empresa = $id_documento_empresa");
	}
}