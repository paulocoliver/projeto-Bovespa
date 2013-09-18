<?php
include_once 'Base.php';

class Empresa extends Base{

	public function getEmpresas($ch = 'A', $importar = false){
		
		$this->Connect();
		$sql = "SELECT * FROM empresa WHERE ".($importar ? "situacao_registro not like '%Cancelado%'" : 'count_links > 0');
		if($ch){
			$sql .= " AND nome like '$ch%'";
		}
		$sql .= " ORDER BY nome";
		
		$result 	= $this->_mysqli->multi_query($sql);
		$response 	= array();
		
	 	if($result = $this->_mysqli->use_result()){
            while($row = $result->fetch_object()){
            	$response[] = $row;
            }
        }
        return $response;
	}
	
	public function getEmpresa($cvm){
		
		$this->Connect();
		$sql = "SELECT * FROM empresa WHERE cvm = $cvm";
		$result = $this->_mysqli->multi_query($sql);
	 	
		if($result = $this->_mysqli->use_result()){
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
			$this->Connect();
			$this->_mysqli->autocommit(FALSE);
			
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
				if(!$this->_mysqli->query($sql) && $this->_mysqli->errno != 1062){
				    throw new Exception("Multi query failed: (" . $this->_mysqli->errno . ") " . $this->_mysqli->error);
				}
			endwhile;
			
			$this->_mysqli->commit();
			$this->_mysqli->close();
			
		}catch(Exception $erro ){
			
			$this->_mysqli->rollback();
			$this->_mysqli->close();
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