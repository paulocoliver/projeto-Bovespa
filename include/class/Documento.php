<?php
class Documento{

	private $_cvm;
	private $_name;
	private $_data;
	private $_colunas = array();
	private $_valores = array();
	
	public function __construct($cvm, $name, $data){
		$this->_cvm  = $cvm;
		$this->_name = $name;
		$this->_data = $data;
	}
	
	public function Cvm($val=null){
		if($val == null){
			return $this->_cvm;
		}
		$this->_cvm = $val;
	}
	
	public function Name($val=null){
		if($val == null){
			return $this->_name;
		}
		$this->_name = $val;
	}
	
	public function Date($val=null){
		if($val == null){
			return $this->_data;
		}
		$this->_data = $val;
	}
	
	public function Colunas($val=null){
		if($val == null){
			return $this->_colunas;
		}
		$this->_colunas = $val;
	}
	
	public function Valores($val=null){
		if($val == null){
			return $this->_valores;
		}
		$this->_valores = $val;
	}
	
	public function addColuna( $descricao, $tipo = null ){
		array_push(
			$this->_colunas, 
			array(
				'descricao' => $descricao,
				'tipo' => $tipo
			)
		);
	}
	
	public function addValores( array $val ){
		$this->verify($val);
		array_push($this->_valores, $val);
	}
	
	public function debug(){
		$view = array(
			'cvm' 	  => $this->_cvm,
			'name' 	  => $this->_name,
			'data' 	  => $this->_data,
			'colunas' => $this->_colunas,
			'valores' => $this->_valores
		);
		
		echo '<pre>';
			print_r( $view );
		echo '</pre>';
	}
	
	public function verify($array){
		if(count($array) != count($this->_colunas)){
			throw new Exception("Colunas e Campos não são equivalentes");
		}
		return true;
	}
}