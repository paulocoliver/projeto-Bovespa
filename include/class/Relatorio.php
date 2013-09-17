<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Base.php';

class Relatorio extends Base{
	
	private $_mysqli;
	
	public function __construct(){}
	
	public function connect(){
		$this->_mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
	}
	
	public function relatorioDDL(){
		$ddl = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/include/class/ddl_relatorio.txt');
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
	
	public function inserir(Documento $doc){
		
		try{
			$this->connect();
			$this->_mysqli->autocommit(FALSE);
			$id_relatorio = $this->getRelatorioID($doc->Name());
			
			$id_vinculo  = $this->vincularRelatorio(
						$doc->Cvm(),
						$id_relatorio,
						$doc->Date()
				   );
				   
			$id_colunas = $this->getColunasID($doc->Colunas(), $id_relatorio);
			$valores = $doc->Valores();
			
			foreach( $valores AS $array ){
				$i = 0;
				foreach($array AS $val ){
					$query = "
						INSERT INTO relatorio_valor 
					    (id_relatorio_empresa,id_coluna,valor)
						values
						('$id_vinculo','$id_colunas[$i]','$val')
					";
					$this->_mysqli->query($query);
					$i++;
				}
			}
			$this->_mysqli->commit();
			$this->_mysqli->close();
			
		}catch(Exception $erro ){			
			$this->_mysqli->rollback();
			$this->_mysqli->close();
			die($erro->getMessage());
		}
	}
	
	public function vincularRelatorio($cvm,$id_relatorio,$data){
		$query = "
			INSERT INTO relatorio_empresa 
		    (cvm,id_relatorio,data)
			values
			('$cvm','$id_relatorio','$data')
		";
		$this->_mysqli->query($query);
		return $this->_mysqli->insert_id;
	}
	
	public function getRelatorioID($descricao){
		
		$sql    = "SELECT id_relatorio FROM relatorio WHERE descricao = '$descricao'";
		$result = $this->_mysqli->query($sql);
        
		if(!$result->num_rows){
			$this->_mysqli->query("INSERT INTO relatorio (descricao) values ('$descricao')");
			return $this->_mysqli->insert_id;
		}
		return $result->fetch_object()->id_relatorio;
	}
	
	public function getColunasID( array $cols, $id_relatorio ){
		
		$response = array();
		foreach($cols AS $value){
			
			$sql = "
					SELECT id_coluna FROM coluna 
					WHERE descricao = '{$value['descricao']}'
					AND id_relatorio = '$id_relatorio'
				";
			$result = $this->_mysqli->query($sql);
			if(!$result->num_rows){
				
				$this->_mysqli->query("
					INSERT INTO coluna (descricao, id_relatorio, tipo) 
					values 
					('{$value['descricao']}', '$id_relatorio', '{$value['tipo']}')
				");
				
				array_push($response,$this->_mysqli->insert_id);
			}else{
				array_push($response,$result->fetch_object()->id_coluna);
			}
		}
		return $response;
	}
}
