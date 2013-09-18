<?php
include_once 'Abstract.php';

class DocumentoUsuario extends Documento{
	
	public function __construct(){}
	
	public function select($id_documento_usuario=null,$id_usuario){
		try{
			$where = '';
			$sql   = "
					SELECT du.*, d.descricao FROM documento_usuario_empresa du 
					LEFT JOIN documento d on d.id_documento = du.id_documento
				";
			
			if($id_documento_usuario){
				$sql.= " WHERE du.id_documento_usuario_empresa = $id_documento_usuario";
			}
			if($id_usuario){
				$sql.= " WHERE du.id_usuario_empresa = $id_usuario";
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
	
	public function loadDoc($id_documento_usuario){
		$this->_documento = $this->select($id_documento_usuario);
		$this->_colunas   = $this->getValues($id_documento_usuario);
	}
	
	public function newDoc($id_usuario_empresa, $id_documento, $data=null){
		parent::newDoc($id_documento);
		$this->_documento->data = $data;
		$this->_documento->id_usuario_empresa  = $id_usuario_empresa;
	}
	
	public function getValues($id_documento_usuario){
		try{
			$this->Connect();
			$sql = "
					SELECT 
						dv.id_documento_usuario_empresa_valor,
						dv.valor,
						c.codigo,
						c.descricao 
					FROM documento_usuario_empresa_valor dv
					LEFT JOIN coluna c on c.id_coluna = dv.id_coluna
					WHERE dv.id_documento_usuario_empresa = $id_documento_usuario
			";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$response[$row['id_documento_usuario_empresa_valor']] = $row;
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
			
			if(!empty($this->_documento->id_documento_usuario_empresa)){
				$this->deletar($this->_documento->id_documento_usuario_empresa);
			}
			
			$sql = "
					INSERT INTO documento_usuario_empresa
					(id_usuario_empresa,id_documento,data)
					values
					('{$this->_documento->id_usuario_empresa}',{$this->_documento->id_documento},'{$this->_documento->data}')
				";
			
			$this->_mysqli->query($sql);
			$id_documento_usuario = $this->_mysqli->insert_id;
			
			$sql = '';
			foreach($this->_colunas AS $coluna):
				$sql.= "
						INSERT INTO documento_usuario_empresa_valor
						(id_documento_usuario_empresa,id_coluna,valor)
						values
						($id_documento_usuario,{$coluna['id_coluna']},{$coluna['valor']});
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
	
	public function deletar($id_documento_usuario){
		$this->_mysqli->query("DELETE FROM documento_usuario_empresa WHERE id_documento_usuario_empresa = $id_documento_usuario");
	}
}