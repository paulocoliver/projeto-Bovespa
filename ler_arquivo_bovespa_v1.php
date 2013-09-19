<?php
$new_content = array();
$zip1 = zip_open('files/'.$filename_open.'.zip');
while ($zip_entry1 = zip_read($zip1)){
	$zip_nome1 = zip_entry_name($zip_entry1);
	$filesize1 = zip_entry_filesize($zip_entry1);
	if (strpos($zip_nome1, '.itr')) {
		$contents1 = zip_entry_read($zip_entry1, $filesize1);
		
		$root = $_SERVER['DOCUMENT_ROOT'].'/files/';
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
	}
}

function getValue(&$row){
	for($i = 0; $i < 7 ; $i++  ){
		$value = (float)$row->{"ValorConta$i"};
		if($value != 0){
			$row->{"ValorConta$i"} = 0;
			return number_format($value, 0, '', '.');
		}
	}
	return 0;
}

ksort($new_content);
foreach ($new_content AS $k1 => $row1):
		ksort($row1);
		foreach ($row1 AS $row3):
//			$numeroConta = $row3->PlanoConta->NumeroConta;
//		 	echo "INSERT INTO coluna (id_documento,codigo,descricao) values (1,'{$numeroConta}','{$row3->DescricaoConta1}');<Br />";
			$response[] = array(
					'descricao' => (string)$row3->DescricaoConta1,
					'codigo' => (string)$row3->PlanoConta->NumeroConta,
					'val_1'  => getValue($row3),
					'val_2'  => getValue($row3),
					'val_3'  => getValue($row3),
					'val_4'  => getValue($row3)
			);
		endforeach; 
endforeach;
?>