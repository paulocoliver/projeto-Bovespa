<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Segmento/Segmento.php';
	
try{
	if(empty($_GET['id_segmento'])){
		throw new Exception('id_segmento nao encontrado');
	}
	
	$segmento = new Segmento();
	$response = array('success' => true, 'empresas' => $segmento->selectEmpresa($_GET['id_segmento']) );
	
}catch(Exception $erro){
	$response = array('success' => false, 'error' => $erro->getMessage());
}

echo json_encode($response);