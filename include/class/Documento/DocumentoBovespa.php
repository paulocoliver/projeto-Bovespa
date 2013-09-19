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
	
	public function setValue($key, $valor, $total, $valor_ano_anterior, $total_ano_anterior){
		if(empty($this->_colunas[$key])){
			throw new Exception("Nenhuma coluna encontrada com a chave $key");
		}
		$this->_colunas[$key]['valor'] = $valor;
		$this->_colunas[$key]['total'] = $total;
		$this->_colunas[$key]['valor_ano_anterior'] = $valor_ano_anterior;
		$this->_colunas[$key]['total_ano_anterior'] = $total_ano_anterior;
	}
	
	public function setValueByCode( $id_documento, $codigo, $descricao, $valor, $total, $valor_ano_anterior, $total_ano_anterior){
		foreach( $this->_colunas AS $key => $value ):
			if(trim($this->_colunas[$key]['codigo']) == trim($codigo)){
				$this->setValue($key,$valor, $total,$valor_ano_anterior,$total_ano_anterior);	
				return;
			}
		endforeach;
		$key = $this->insertColuna($id_documento, $codigo, $descricao);
		$this->setValue($key,$valor, $total,$valor_ano_anterior,$total_ano_anterior);
	}
	
	public function insertColuna($id_documento, $codigo, $descricao){
		
		try{
			$this->Connect();
			if(mb_detect_encoding($descricao) == 'UTF-8'){
				$descricao = utf8_decode($descricao);
			}
			$sql = "
				INSERT INTO coluna
				(id_documento,codigo,descricao)
				values
				($id_documento, '$codigo','$descricao');
			";
			
			$this->_mysqli->query($sql);
			$id_coluna = $this->_mysqli->insert_id;
			
			$this->_colunas[$id_coluna] = array(
											'id_coluna'    => $id_coluna,
											'id_documento' => $id_documento,
											'codigo' 	   => $codigo,
											'descricao'    => $descricao
										);
			return $id_coluna;
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
					SELECT id_coluna, valor, total, valor_ano_anterior, total_ano_anterior FROM documento_empresa_valor 
					WHERE id_documento_empresa = $id_documento_empresa 
			";
			$result = $this->_mysqli->multi_query($sql);
        
			$response 	= array();
		 	if($result = $this->_mysqli->use_result()){
	            while($row = $result->fetch_assoc()){
	            	$this->setValue(
	            		$row['id_coluna'],
	            		$row['valor'],
	            		$row['total'],
	            		$row['valor_ano_anterior'],
	            		$row['total_ano_anterior']
	            	);
	            }
	        }
			
		}catch(Exception $erro){
			die($erro->getMessage());
		}
	}
	
	private function _tratarNum($num){
		return !empty($num) ? str_replace(',','.',str_replace('.','',$num)) : 'NULL'; 
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
				$valor = $this->_tratarNum($coluna['valor']);
				$total = $this->_tratarNum($coluna['total']); 
				$valor_ano_anterior = $this->_tratarNum($coluna['valor_ano_anterior']);
				$total_ano_anterior = $this->_tratarNum($coluna['total_ano_anterior']);
				
				if($insert){
					$sql = "
						INSERT INTO documento_empresa_valor
						(id_documento_empresa,id_coluna,valor, total, valor_ano_anterior, total_ano_anterior)
						values
						($id_documento_empresa,{$coluna['id_coluna']},$valor, $total, $valor_ano_anterior, $total_ano_anterior );
					";
				}else{
					$sql = "
						UPDATE documento_empresa_valor set 
							valor = $valor,
							total = $total,
							valor_ano_anterior = $valor_ano_anterior,
							total_ano_anterior = $total_ano_anterior
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