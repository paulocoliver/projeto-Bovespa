<?php
$content_new = array();
$zip = zip_open('files/'.$filename_open.'.WTL');
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
					
					
					$val_1 = !empty($val_1) ? number_format($val_1, 0, '', '.') : '';
					$val_2 = !empty($val_2) ? number_format($val_2, 0, '', '.') : '';
					$val_3 = !empty($val_3) ? number_format($val_3, 0, '', '.') : '';
					$val_4 = !empty($val_4) ? number_format($val_4, 0, '', '.') : '';
					
					$content_new[$name][] = array(
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
?>

<?php 
$data1 = '';
$data2 = '';
if (!empty($res_link->data)) {
	$new_date = explode('-', $res_link->data);
	
	$data1 = $new_date[2].'/'.$new_date[1].'/'.$new_date[0];
	$data2 = '31/12/'.($new_date[0]-1);
}
foreach ($content_new AS $k => $row1):
?>
	<table class="table table-bordered table-striped">
		<tr>
			<th colspan="6" style="text-align: center;"><?php echo $k ?></th>
		</tr>
		<tr>
			<th style="text-align: center;">Código da Conta</th>
			<th style="text-align: center;">Descrição da Conta</th>
			<th style="text-align: center;"><?php //echo $data1 ?></th>
			<th style="text-align: center;"><?php //echo $data2 ?></th>
			<th style="text-align: center;"></th>
			<th style="text-align: center;"></th>
		</tr>
	<?php 
	foreach ($row1 AS $row2):
	?>
		<tr>
			<td><?php echo $row2['cod_conta'] ?></td>
			<td><?php echo utf8_encode($row2['title']) ?></td>
			<td><?php echo $row2['val_1'] ?></td>
			<td><?php echo $row2['val_2'] ?></td>
			<td><?php echo $row2['val_3'] ?></td>
			<td><?php echo $row2['val_4'] ?></td>
		</tr>
	<?php
	endforeach; 
	?>
	</table>
	<br /><br />
<?php 
endforeach;
?>


