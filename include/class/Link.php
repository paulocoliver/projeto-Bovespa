<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Base.php';

class Link extends Base{

	private $_data_atualizacao = '1990-01-01';
	
	public function getOutput( $cvm )
	{
		return parent::getOutput("http://www.bmfbovespa.com.br/cias-listadas/empresas-listadas/HistoricoFormularioReferencia.aspx?codigoCVM=$cvm&tipo=itr&ano=0&idioma=pt-br");
	}
	
	public function getLinks( $cvm )
	{
		
		$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
		
		$sql = "SELECT link.id_link, link.cvm, link.descricao, link.link, DATE_FORMAT(link.data,'%d/%m/%Y') AS data FROM link WHERE cvm = $cvm ORDER BY link.data DESC";
		$result 	= $mysqli->multi_query($sql);
		$response 	= array();
		
	 	if($result = $mysqli->use_result()){
            while($row = $result->fetch_object()){
            	$response[] = $row;
            }
        }
        return $response;
	}
	
	public function getLink($id_link){
	
		$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
		if (mysqli_connect_errno()) {
			throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
	
		$sql = "SELECT * FROM link WHERE id_link = $id_link";
		$result = $mysqli->multi_query($sql);
		 
		if($result = $mysqli->use_result()){
			return $result->fetch_object();
		}
		return NULL;
	}
	
	public function inserir( $output, $cvm )
	{
		try{
			$mysqli = new mysqli($this->_domain, $this->_user, $this->_password, 'projeto_db');
			if (mysqli_connect_errno()) {
			    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
			}
			
			$mysqli->autocommit(FALSE);
			
			$html = $output->find('.listaAcessos a');
			for( $i = 0 ; $i < count($html) ; $i++):

				$texto = utf8_decode($html[$i]->plaintext);
				preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $html[$i]->plaintext, $data);
				preg_match('/http.[^\)\']+/', $html[$i]->href, $link);
				
				$docDate    = substr($data[0],-4).'-'.substr($data[0],3,2).'-'.substr($data[0],0,2);

				if( strtotime($docDate) > strtotime($this->_data_atualizacao)){
					$sql = "
						insert into link (cvm,data,descricao,link) values (
							$cvm,	
							'$docDate',
							'{$texto}',							
							'$link[0]'
						); 
					";
							
					if(!$mysqli->query($sql) && $mysqli->errno != 1062){
					    throw new Exception("Multi query failed: (" . $mysqli->errno . ") " . $mysqli->error);
					}
				}
			endfor;
			
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
		$html 	= $output->find('.listaAcessos ul li a');
		$lista 	= array();
		for( $i = 0 ; $i < count($html) ; $i++):
			
			preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $html[$i]->plaintext, $data);
			preg_match('/http.[^\)\']+/', $html[$i]->href, $link);
			
			$lista[]= array(
				'data' 		=> $data[0],
				'descricao' => $html[$i]->plaintext,
				'link' 		=> $link[0],
				'origiLink' => $html[$i]->href
			);
			
		endfor;

		echo '<pre>';
			print_r($lista);
		echo '<pre>';
		exit;
	}
}