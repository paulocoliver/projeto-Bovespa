<?php
include 'include/class/Documento/DocumentoBovespa.php';

$documento = new DocumentoBovespa(60);

$documento->loadDoc(9);
echo '<pre>';
	print_r($documento->getColunas());
echo '</pre>';
exit;

$documento->setData('31-03-2010');

$values = array( '76' => 0,
		'77' => 3213.12,
		'78' => 32.13,
		'79' => 23.21,
		'80' => 2.13,
		'81' => 12321312.00,
		'82' => 0.00,
		'83' => 0.00,
		'84' => 0.00,
		'85' => 0.00,
		'86' => 0.00,
		'87' => 0.00,
		'88' => 0.00,
		'89' => 0.00,
		'90' => 0.00,
		'91' => 0.00,
		'92' => 0.00,
		'93' => 0.00,
		'94' => 0.00,
		'95' => 0.00,
		'96' => 0.00,
		'97' => 0.00,
		'98' => 0.00,
		'99' => 0.00,
		'100' => 0.00,
		'101' => 0.00,
		'102' => 0.00,
		'103' => 0.00,
		'104' => 0.00,
		'105' => 0.00,
		'106' => 0.00,
		'107' => 0.00,
		'108' => 0.00,
		'109' => 0.00,
		'110' => 0.00,
		'111' => 0.00,
		'112' => 0.00,
);
foreach($values AS $key => $valor){
	$documento->setValue($key, $valor);
}

$documento->inserir();