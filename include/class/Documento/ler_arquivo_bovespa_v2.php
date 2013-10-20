<?php
$new_content = array();
$root = $_SERVER['DOCUMENT_ROOT'].'/files/';
$zip = zip_open($root.$filename_open.'.WTL');
if ($zip) {
	while ($zip_entry = zip_read($zip)) {
		$name = zip_entry_name($zip_entry);
			
		$filesize = zip_entry_filesize($zip_entry);
		if (zip_entry_open($zip, $zip_entry) && strpos($name, '.001')) {//
			$contents = zip_entry_read($zip_entry, $filesize);
			$contents = explode("\n", $contents);
			foreach ($contents AS $row) {
				
				//echo $row.'<br>';
				$title = trim(substr($row, 27, 48));
				if (!empty($title)) {
					$data  = trim(substr($row, 0, 27));
					$cod_conta = trim(substr($row, 14, 13));
					$val_1 = (double) trim(substr($row, 75, 15));
					$val_2 = (double) trim(substr($row, 90, 15));
					$val_3 = (double) trim(substr($row, 105, 15));
					$val_4 = (double) trim(substr($row, 120, 15));
					
					$new_content[$name][] = array(
						'data' => $data,
						'cod_conta' => $cod_conta,
						'title' => $title,
						'val_1' => $val_1,
						'val_2' => $val_2,
						'val_3' => $val_3,
						'val_4' => $val_4,
						'row' => $row,
					);
				}
			}
			zip_entry_close($zip_entry);
		}
	}
	zip_close($zip);
}
@unlink($root.$filename_open.'.WTL');
?>

<?php 
$response = array();
foreach ($new_content AS $k => $conta ):
	foreach($conta AS $linha ){
		
		if(((int)$linha['cod_conta']) == 3){
			$response[] = array(
							'descricao' => $linha['title'],
							'codigo' => $linha['cod_conta'],
							'val_1'  => $linha['val_1'],
							'val_2'  => $linha['val_2'],
							'val_3'  => $linha['val_3'],
							'val_4'  => $linha['val_4']
						);
		}
	}
endforeach;
?>