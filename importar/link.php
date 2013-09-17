<?php
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Empresa.php';
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Link.php';

$step = !empty($_GET['step']) ? $_GET['step'] : 0;
$link 	 = new Link();
$empresa = new Empresa();

$lista    = $link->getLista();
$empresas = $empresa->getEmpresas($lista[$step], true);

foreach($empresas AS $empresa):
	$output = $link->getOutput($empresa->cvm);
	$link->inserir($output,$empresa->cvm);
//	$link->visualizar($output);
//	exit;
endforeach;

$step++;
if($step < count($lista)){
	header("Location: {$_SERVER['SCRIPT_NAME']}?step=$step");
}
die('Processo concluido!');