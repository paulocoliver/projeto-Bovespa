<?php
include_once 'Documento.php';

class DocumentoUsuario extends Documento{
	
	private $_id_usuario_empresa;
	
	public function __construct($id_usuario_empresa){
		$this->_id_usuario_empresa = $id_usuario_empresa;
	}
	
	
	public function select($id_documento_usuario=null){
		try{
			$where = '';
			$sql   = "
					SELECT 
						du.id_documento_usuario_empresa, 
						du.id_documento,
						DATE_FORMAT(du.data,'%d-%m-%Y') AS data,
						d.descricao FROM documento_usuario_empresa du 
					LEFT JOIN documento d on d.id_documento = du.id_documento
					WHERE du.id_usuario_empresa = {$this->_id_usuario_empresa}
				";
			
			if(!empty($id_documento_usuario)){
				$sql.= " AND du.id_documento_usuario_empresa = $id_documento_usuario";
			}
			$sql.= " ORDER by du.data";
			
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
	
	public function loadDoc($id_documento_usuario){
		$doc = $this->select($id_documento_usuario);
		$this->_documento = $doc[0];
		$this->setData($this->_documento['data']);
		$this->_colunas   = $this->getDocCols($this->_documento['id_documento']);
		
		$this->loadValues($id_documento_usuario);
	}
	
	public function getDataEmUso(){
			$this->Connect();
			$sql = "
					SELECT DATE_FORMAT(data,'%d-%m-%Y') AS data FROM documento_usuario_empresa
					WHERE id_documento_usuario_empresa = {$this->_id_usuario_empresa}
			";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[] = $row['data'];
	            }
	        }
	        return $response;
	}
	
	public function loadValues($id_documento_usuario){
		try{
			$this->Connect();
			$sql = "
					SELECT id_coluna, valor FROM documento_usuario_empresa_valor 
					WHERE id_documento_usuario_empresa = $id_documento_usuario 
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
			
			if(!isset($this->_documento['id_documento_usuario_empresa'])){
				$insert = true;
				$sql = "
					INSERT INTO documento_usuario_empresa
					(id_usuario_empresa,id_documento,data)
					values
					({$this->_id_usuario_empresa},{$this->_documento['id_documento']},'{$this->_data}')
				";
				
				$this->_mysqli->query($sql);
				$id_documento_usuario_empresa = $this->_mysqli->insert_id;
				
			}else{
				$id_documento_usuario_empresa = $this->_documento['id_documento_usuario_empresa'];
			}
			
			foreach($this->_colunas AS $coluna):
				$valor = !empty($coluna['valor']) ? str_replace(',','.',str_replace('.','',$coluna['valor'])) : 0; 
				
				if($insert){
					$sql = "
						INSERT INTO documento_usuario_empresa_valor
						(id_documento_usuario_empresa,id_coluna,valor)
						values
						($id_documento_usuario_empresa,{$coluna['id_coluna']},$valor);
					";
				}else{
					$sql = "
						UPDATE documento_usuario_empresa_valor set valor = $valor 
						WHERE id_coluna = {$coluna['id_coluna']} AND id_documento_usuario_empresa = $id_documento_usuario_empresa
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
	
	public function deletar($id_documento_usuario_empresa){
		$this->_mysqli->query("DELETE FROM documento_usuario_empresa WHERE id_documento_usuario_empresa = $id_documento_usuario_empresa");
	}
}