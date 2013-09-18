<?php
include_once 'Abstract.php';

class DocumentoBovespa extends Documento{
	
	public function __construct(){}
	
	public function select($id_documento_empresa=null){
		try{
			$where = '';
			$sql   = "
					SELECT de.*, d.descricao FROM documento_empresa de 
					LEFT JOIN documento d on d.id_documento = de.id_documento
				";
			
			if($id_documento_empresa){
				$sql.= " WHERE de.id_documento_empresa = $id_documento_empresa";
			}
			$this->Connect();
			
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[$row['id_coluna']] = $row;
	            }
	        }
	        return (count($response) == 1) ? $response[0] : $response;
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	public function loadDoc($id_documento_empresa){
		$this->_documento = $this->select($id_documento_empresa);
		$this->_colunas   = $this->getValues($id_documento_empresa);
	}
	
	public function newDoc($cvm, $data, $id_documento){
		parent::newDoc($id_documento);
		$this->_documento->data = $data;
		$this->_documento->cvm  = $cvm;
	}
	
	public function getValues($id_documento_empresa){
		try{
			$this->Connect();
			$sql = "
					SELECT 
						dv.id_documento_empresa_valor,
						dv.valor,
						c.codigo,
						c.descricao 
					FROM documento_empresa_valor dv
					LEFT JOIN coluna c on c.id_coluna = dv.id_coluna
					WHERE dv.id_documento_empresa = $id_documento_empresa
			";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[$row['id_documento_empresa_valor']] = $row;
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
	
	public function inserir(){
		try{
			$this->Connect();
			$this->_mysqli->autocommit(FALSE);
			
			if(!empty($this->_documento->id_documento_empresa)){
				$this->deletar($this->_documento->id_documento_empresa);
			}
			
			$sql = "
					INSERT INTO documento_empresa 
					(cvm,id_documento,data)
					values
					('{$this->_documento->cvm}',{$this->_documento->id_documento},'{$this->_documento->data}')
				";
			
			$this->_mysqli->query($sql);
			$id_documento_empresa = $this->_mysqli->insert_id;
			
			$sql = '';
			foreach($this->_colunas AS $coluna):
				$sql.= "
						INSERT INTO documento_empresa_valor
						(id_documento_empresa,id_coluna,valor)
						values
						($id_documento_empresa,{$coluna['id_coluna']},{$coluna['valor']});
					";
			endforeach;
			
			$this->_mysqli->multi_query($sql);
			
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