<?php
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Empresa.php';

$step = !empty($_GET['step']) ? $_GET['step'] : 0;
$empresa = new Empresa();

$output = $empresa->getOutput($step);
$empresa->createDLL();
$empresa->inserir($output);

$lista = $empresa->getLista();
$step++;
if($step < count($lista)){
	header("Location: {$_SERVER['SCRIPT_NAME']}?step=$step");
}

die('Processo concluido!');