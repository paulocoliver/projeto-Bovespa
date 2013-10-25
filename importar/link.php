<?php
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Empresa.php';
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Link.php';

$step = isset($_GET['step']) ? $_GET['step'] : 0;
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

die('Processo concluido!');