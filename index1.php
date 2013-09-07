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

						//echo $numeroConta;
						//exit;

						$new_content[$conta][$numeroConta] = $row;

						echo '<pre>';
						print_r($row);
						echo '</pre>';


						/*$codigoTipoIF = (int)$row->PlanoConta->VersaoPlanoConta->CodigoTipoInformacaoFinanceira;
						$codigoTipoDF = (int)$row->PlanoConta->VersaoPlanoConta->CodigoTipoDemonstracaoFinanceira;
		
						if (empty($codigoTipoIF)) $codigoTipoIF = 'nulo';
						if (empty($codigoTipoDF)) $codigoTipoDF = 'nulo';
		
						$new_content[$codigoTipoIF][$codigoTipoDF][] = $row;*/

						

						
					}
					zip_entry_close($zip_entry);
				}
			}
			zip_close($zip);
		}
		
	}
}

/*
if ($zip) {
	while ($zip_entry = zip_read($zip)) {
    	$name = zip_entry_name($zip_entry);
    	$filesize = zip_entry_filesize($zip_entry);
    	if (zip_entry_open($zip, $zip_entry) && $name == 'InfoFinaDFin.xml') {
			$contents = zip_entry_read($zip_entry, $filesize);
			$contents = simplexml_load_string($contents);
			
			$new_content = array();
			foreach ($contents AS $k => $row) {
				$codigoTipoIF = (int)$row->PlanoConta->VersaoPlanoConta->CodigoTipoInformacaoFinanceira;
				$codigoTipoDF = (int)$row->PlanoConta->VersaoPlanoConta->CodigoTipoDemonstracaoFinanceira;
				
				if (empty($codigoTipoIF)) $codigoTipoIF = 'nulo';
				if (empty($codigoTipoDF)) $codigoTipoDF = 'nulo';
				
				$new_content[$codigoTipoIF][$codigoTipoDF][] = $row;
			}
      		zip_entry_close($zip_entry);
      	}
  	}
	zip_close($zip);
}
*/
ksort($new_content);
$num_contas = array_keys($new_content);
$contaAtiva = current($num_contas);

$titles_contas = array(
	1 => 'Balanço Patrimonial Ativo',
	2 => 'Balanço Patrimonial Passivo',
	3 => 'Demonstração do Resultado',
	4 => 'Demonstração do Resultado Abrangente',
	5 => 'Demonstração das Mutações do Patrimônio Líquido',
	6 => 'Demonstração do Fluxo de Caixa',
	7 => 'Demonstração de Valor Adicionado',
);
?>

<ul class="nav nav-tabs" id="myTab">
	<?php
	foreach ($num_contas as $num):
	?>
	<li class="<?php echo $contaAtiva == $num ? 'active' : '' ?>"><a href="#conta-<?php echo $num ?>"><?php echo $titles_contas[$num] ?></a></li>
	<?php
	endforeach;
	?>
</ul>
 


<div id="myTabContent" class="tab-content">
<?php
foreach ($new_content AS $k1 => $row1):
?>
	<div class="tab-pane <?php echo $contaAtiva == $k1 ? 'active' : '' ?>" id="conta-<?php echo $k1 ?>">
		<table class="table table-bordered table-striped">
			<tr>
				<th colspan="9" style="text-align: center;"><?php echo $titles_contas[$k1] ?></th>
			</tr>
			<tr>
				<th style="text-align: center;">Código da Conta</th>
				<th style="text-align: center;">Descrição da Conta</th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
				<th style="text-align: center;"></th>
			</tr>
		<?php 
		ksort($row1);
		foreach ($row1 AS $row3):
			$title = $row3->DescricaoConta1;
			$numeroConta = $row3->PlanoConta->NumeroConta;
			
			
			$var1 = (double)$row3->ValorConta1;
			$var1 = !empty($var1) ? number_format($var1, 0, '', '.') : '';

			$var2 = $row3->ValorConta2;
			$var2 = (double)$row3->ValorConta2;
			$var2 = !empty($var2) ? number_format($var2, 0, '', '.') : '';
			
			$var3 = (double)$row3->ValorConta3;
			$var3 = !empty($var3) ? number_format($var3, 0, '', '.') : '';
			
			$var4 = (double)$row3->ValorConta4;
			$var4 = !empty($var4) ? number_format($var4, 0, '', '.') : '';
			
			$var5 = (double)$row3->ValorConta5;
			$var5 = !empty($var5) ? number_format($var5, 0, '', '.') : '';
			
			$var6 = (double)$row3->ValorConta6;
			$var6 = !empty($var6) ? number_format($var6, 0, '', '.') : '';
			
			$var7 = (double)$row3->ValorConta7;
			$var7 = !empty($var7) ? number_format($var7, 0, '', '.') : '';
			
		?>
			<tr>
				<td><?php echo $numeroConta ?></td>
				<td style="white-space: nowrap;"><?php echo $title ?></td>
				<td><?php echo $var1 ?></td>
				<td><?php echo $var2 ?></td>
				<td><?php echo $var3 ?></td>
				<td><?php echo $var4 ?></td>
				<td><?php echo $var5 ?></td>
				<td><?php echo $var6 ?></td>
				<td><?php echo $var7 ?></td>
				<?php
				/* 
				<td>
					<?php 
					echo '<pre>';
					print_r($row3->PlanoConta);
					echo '</pre>';
					?>
				</td>
				*/
				?>
			</tr>
		<?php
		endforeach; 
		?>
		</table>
	</div>
<?php 
endforeach;
?>
</div>

<script type="text/javascript">
		console.log('dsadas');
	$(document).ready(function() {
// 	//$('#myTab a:last').tab('show');
// 	//$('#myTab a:first').tab('show');
		
// 	$('#myTab').on('click',(function (e) {
// 		console.log('dsadas');
// 		e.preventDefault();
// 		$(this).tab('show');
// 	});

		$('#myTab a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		
 		
	});
</script>
