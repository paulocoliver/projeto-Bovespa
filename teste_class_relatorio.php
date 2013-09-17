<?php
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Relatorio.php';
include $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento.php';

$relatorio = new Relatorio();

//cria DDL das tabelas de relatorio 
$relatorio->relatorioDDL(); 

//cria novo documento
$doc = new Documento('15253','dre','2010-03-31'); 

//define colunas do documento
$doc->addColuna('coluna 1', 'ativo');
$doc->addColuna('coluna 2', 'ativo');
$doc->addColuna('coluna 3', 'passivo');
$doc->addColuna('coluna 4', 'passivo');
$doc->addColuna('coluna 5');

//insere valores
//quantidade de itens deve ser equivalente a quantidade de colunas
$doc->addValores(
		array( 
			'3.01',  
			'Receita de Venda de Bens e/ou Serviços',  
			'100.20',  
			'1000.20',
			'150.80'
		)
	);
$doc->addValores(
		array( 
			'3.20',  
			'Custo dos Bens e/ou Serviços Vendidos',  
			'10.20',  
			'1230.20',  
			'150.80'  
		)
	);

//visualizar documento
$doc->debug();

//inserir documento
$relatorio->inserir($doc);