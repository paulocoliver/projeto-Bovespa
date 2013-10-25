<?php
$new_content = array();
$root = $_SERVER['DOCUMENT_ROOT'].'/files/';

$zip1 = zip_open($root.$filename_open.'.zip');
@$zip_entry1 = zip_read($zip1);

do{
	if(!$zip_entry1){
		throw new Exception('Arquivo nao encontrado ', '05');
	}
	$zip_nome1 = zip_entry_name($zip_entry1);
	$filesize1 = zip_entry_filesize($zip_entry1);
	if (strpos($zip_nome1, '.itr')) {
		$contents1 = zip_entry_read($zip_entry1, $filesize1);		
		
		$hedef_yol = $root.$zip_nome1;
		touch($hedef_yol);
		$yeni_dosya = fopen($hedef_yol, 'w+');
		fwrite($yeni_dosya, $contents1);
		fclose($yeni_dosya);
		
		$zip = zip_open($hedef_yol);
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
				$name = zip_entry_name($zip_entry);
				$filesize = zip_entry_filesize($zip_entry);
				if (zip_entry_open($zip, $zip_entry) && $name == 'InfoFinaDFin.xml') {
					$contents = zip_entry_read($zip_entry, $filesize);
					$contents = simplexml_load_string($contents);
						
					foreach ($contents AS $k => $row) {

						$numeroConta = (string)$row->PlanoConta->NumeroConta;
						$conta = (int)substr($numeroConta, 0, 1);

						if ($conta != 3)
							continue;

						$new_content[$conta][$numeroConta] = $row;
					}
					zip_entry_close($zip_entry);
				}
			}
			zip_close($zip);
		}
		@unlink($hedef_yol);
	}
}while (@$zip_entry1 = zip_read($zip1));
@unlink($root.$filename_open.'.zip');

function getValue($row){
	
	$found=array();
	$response = array(
				'val_1' => 0,
				'val_2' => 0,
				'val_3' => 0,
				'val_4' => 0
			);
	
	for($i = 1; $i < 7 ; $i++  ){
		$value = (float)$row->{"ValorConta$i"};
		if($value > 0 || $value < 0){
			$found[] = $value;
		}
	}
	
	switch(count($found)):
		case 1 :
			$response['val_1'] = $found[0];
		break;
		case 2 :
			$response['val_1'] = $found[0];
			$response['val_3'] = $found[1];
		break;
		case 3 :
			$response['val_1'] = 0;
			$response['val_2'] = $found[0];
			$response['val_3'] = $found[1];
			$response['val_4'] = $found[2];
		break;
		case 4 :
			$response['val_1'] = $found[0];
			$response['val_2'] = $found[1];
			$response['val_3'] = $found[2];
			$response['val_4'] = $found[3];
		break;
	endswitch;
	return $response;
}

ksort($new_content);
foreach ($new_content AS $k1 => $row1):
		ksort($row1);
		foreach ($row1 AS $row3):
			$values = getValue($row3);
//			$numeroConta = $row3->PlanoConta->NumeroConta;
//		 	echo "INSERT INTO coluna (id_documento,codigo,descricao) values (1,'{$numeroConta}','{$row3->DescricaoConta1}');<Br />";
			$response[] = array(
					'descricao' => (string)$row3->DescricaoConta1,
					'codigo' => (string)$row3->PlanoConta->NumeroConta,
					'val_1'  => $values['val_1'],
					'val_2'  => $values['val_2'],
					'val_3'  => $values['val_3'],
					'val_4'  => $values['val_4']
			);
		endforeach; 
endforeach;
?>