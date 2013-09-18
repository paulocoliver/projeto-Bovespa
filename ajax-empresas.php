<?php 
include_once 'include/class/Bovespa/Empresa.php';
	
$ch  = !empty($_GET['q']) ? $_GET['q'] : null;
$cvm = !empty($_GET['cvm']) ? $_GET['cvm'] : null;
$empresa = new Empresa();

//$listaCh  = $empresa->getLista();

if (!empty($cvm)) {
	$ret = $empresa->getEmpresa($cvm);
	$ret->nome = utf8_encode($ret->nome);
} else {
	
	
	$limit = !empty($_GET['page_limit']) ? $_GET['page_limit'] : 10;
	$page  = !empty($_GET['page']) ? $_GET['page'] : 1;
	
	$result = $empresa->getEmpresas($ch);
	$total = count($result);
	/*
	$result_new = array();
	for ($i = ($limit * $page - 1), $j = 0; $i < $total && $j < 10; $i++, $j++) {
		$result[$i]->nome = utf8_encode($result[$i]->nome);
		$result_new[] = $result[$i];
	}*/
	
	$offset = ($page - 1) * 10;
	$result_new = array_slice($result, $offset, 10);
	foreach ($result_new as $i => $value) {
		$result_new[$i]->nome = utf8_encode($value->nome);
	}
	
	$ret = array(
		'total' => count($result),
		'empresas' => $result_new,
	);
}
echo json_encode($ret);