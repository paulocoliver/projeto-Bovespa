<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Base.php';

class Empresa extends Base{

	public function getEmpresas($ch = 'A', $importar = false){
		
		$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
		
		$sql = "SELECT * FROM empresa WHERE ".($importar ? "situacao_registro not like '%Cancelado%'" : 'count_links > 0');
		if($ch){
			$sql .= " AND nome like '$ch%'";
		}
		$sql .= " ORDER BY nome";
		
		$result 	= $mysqli->multi_query($sql);
		$response 	= array();
		
	 	if($result = $mysqli->use_result()){
            while($row = $result->fetch_object()){
            	$response[] = $row;
            }
        }
        return $response;
	}
	
	public function getEmpresa($cvm){
		
		$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
		
		$sql = "SELECT * FROM empresa WHERE cvm = $cvm";
		$result = $mysqli->multi_query($sql);
	 	
		if($result = $mysqli->use_result()){
            return $result->fetch_object();
        }
        return NULL;
	}
	
	public function getOutput($step = 0)
	{
		if ($step == 2) {
			return parent::getOutput("http://trabalho-bovespa.dev/empresac.php");
		}
		return parent::getOutput("http://cvmweb.cvm.gov.br/SWB/Sistemas/SCW/CPublica/CiaAb/FormBuscaCiaAbOrdAlf.aspx?LetraInicial={$this->_lista[$step]}");
	}
	
	public function inserir($output)
	{
		try{
			$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
			if (mysqli_connect_errno()) {
			    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
			}
			
			$mysqli->autocommit(FALSE);
			
			$i = 0;
			$html  = $output->find('#dlCiasCdCVM tr > a');
			
			while($i < count($html)):
				$sql = "
					insert into empresa (cnpj,nome,cvm,situacao_registro) values (
						'{$html[$i++]->plaintext}',
						'{$html[$i++]->plaintext}',
						{$html[$i++]->plaintext},
						'{$html[$i++]->plaintext}'
					); 
				";
						
				//ignora erro com registro duplicados
				if(!$mysqli->query($sql) && $mysqli->errno != 1062){
				    throw new Exception("Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error);
				}
			endwhile;
			
			$mysqli->commit();
			$mysqli->close();
			
		}catch(Exception $erro ){
			
			$mysqli->rollback();
			$mysqli->close();
			die($erro->getMessage());
		}
	}
	
	public function visualizar($output)
	{
		$i 		= 0;
		$html 	= $output->find('#dlCiasCdCVM tr > a');
		$lista 	= array();
		while($i < count($html)):
			$lista[]= array(
				'cnpj' => $html[$i++]->plaintext,
				'nome' => $html[$i++]->plaintext,
				'cvm' => $html[$i++]->plaintext,
				'situacao_registro' => $html[$i++]->plaintext
			);
		endwhile;

		echo '<pre>';
			print_r($lista);
		echo '<pre>';
	}
}