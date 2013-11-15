<?php 
header('Content-Encoding: UFT8');
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/Documento.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Segmento/Segmento.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Auth/Usuario.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Link.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Helper/Function.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/BuscarDocumento.php';

try{
	
	$grafico_x = $_POST['grafico_col_x'];
	$grafico_y = $_POST['grafico_col_y'];
	
	$helper = new Helper_Function();
	$id_documento_usuario = $_POST['documento_usuario'];
	$cvm = $_POST['cvm'];
	
	if(empty($id_documento_usuario)||empty($cvm)){
		die("id_documento ou cvm nao encontrado");	
	}
	$auth = new Auth_Usuario();
	$docUsuario = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
	$docUsuario->loadDoc($id_documento_usuario);
	
	$documento = new Documento();
	$documento->setData($docUsuario->getData());
	$documento->newDoc(1);
	
	$segmento = new Segmento();
	$segmento->mediaEmpresas($documento, $cvm);
	
	$colSegmento = $documento->getColunas();
	$colUsuario = $docUsuario->getColunas();
	
	
	$grafico_res = array();
	$empresasSeg = $segmento->selectEmpresa($_POST['id_segmento']);
	foreach ($empresasSeg as $empresaSeg) {
		$link = new Link();
		$link = $link->getLinkByCVM($empresaSeg['cvm'], $docUsuario->getData());
		if($link == null){
			continue;
		} else {
			$buscarDoc  = new BuscarDocumento();
			$docBovespa = $buscarDoc->getDocumento($link->id_link);
			$cols = $docBovespa->getColunas();
			
			$cols[$grafico_x]['descricao'];
			
			$val_x = !empty($cols[$grafico_x]['valor']) ? $cols[$grafico_x]['valor'] : 0;
			$val_y = !empty($cols[$grafico_y]['valor']) ? $cols[$grafico_y]['valor'] : 0;
		}
		
		$grafico_res[] = array(
			'cvm' => $empresaSeg['cvm'],	
			'nome' => $empresaSeg['nome'],
			'x' => number_format($val_x, 2, '.', ''),
			'y' => number_format($val_y, 2, '.', '')
		);
	}
	
	$grafico_x_title = utf8_encode(str_replace("'", '', $colSegmento[$grafico_x]['descricao']));
	$grafico_y_title = utf8_encode(str_replace("'", '', $colSegmento[$grafico_y]['descricao']));
	
}catch(Exception $erro ){
	die('ERRO : '.$erro->getMessage());
}
$mes = substr($docUsuario->getData(),5,2);
$ano = substr($docUsuario->getData(),0,4);

switch($mes):
	case '06' :
		$titleColspan = 18;
		$th1 = "01/04/$ano à 30/06/$ano";
		$th2 = "01/01/$ano à 30/06/$ano";
		$th3 = "01/04/".($ano-1)." à 30/06/".($ano-1);
		$th4 = "01/01/".($ano-1)." à 30/06/".($ano-1);
	break;
	
	case '09' :
		$titleColspan = 18;
		$th1 = "01/07/$ano à 30/09/$ano";
		$th2 = "01/01/$ano à 30/09/$ano";
		$th3 = "01/07/".($ano-1)." à 30/09/".($ano-1);
		$th4 = "01/01/".($ano-1)." à 30/09/".($ano-1);
	break;
	
	default :
		$titleColspan = 10;
		$th1 = "01/01/$ano à 31/03/$ano";
		$th3 = "01/01/".($ano-1)." à 30/03/".($ano-1);
	break;
endswitch;
?>

 <div id="chart_div" style="width: 100%; height: 600px;"></div>


<table id="lista_dre">
	<tbody>
		<tr>
			<tr>
				<th colspan="<?php echo $titleColspan?>" style="text-align: center;"><?php echo utf8_encode($docUsuario->getDescricao()); ?></th>
			</tr>
			<tr>
				<th style="text-align: center;">Código da Conta</th>
				<th style="text-align: center;">Descrição da Conta</th>
				<th style="text-align: center;" colspan="4"><?php echo $th1?></th>
				<?php if(isset($th2)):?>
					<th style="text-align: center;" colspan="4"><?php echo $th2?></th>
				<?php endif;?>
				
				<th style="text-align: center;" colspan="4"><?php echo $th3?></th>
				
				<?php if(isset($th4)):?>
					<th style="text-align: center;" colspan="4"><?php echo $th4?></th>
				<?php endif;?>
			</tr>
			<tr class="col-description">
				<td colspan="2"></td>
				<td>Minha Empresa</td>
				<td>Concorrente</td>
				<td>Diff.</td>
				<td>Diff %</td>
				
				<td>Minha Empresa</td>
				<td>Concorrente</td>
				<td>Diff.</td>
				<td>Diff %</td>
				
				<?php if(isset($th2)):?>
					<td>Minha Empresa</td>
					<td>Concorrente</td>
					<td>Diff.</td>
					<td>Diff %</td>
				
					<td>Minha Empresa</td>
					<td>Concorrente</td>
					<td>Diff.</td>
					<td>Diff %</td>
				<?php endif;?>
				
			</tr>
		<?php 
			if(is_array($colUsuario)):
				$i=0;
				foreach ($colUsuario AS $key => $coluna) :
		?>
					<tr style="background-color:<?php echo $i%2==0?'white':'#f5f5f5';?>">
						<td><?php echo utf8_encode($coluna['codigo'])?></td>
						<td><?php echo utf8_encode($coluna['descricao'])?></td>
						
						<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['valor']) ?></td>
						<td class="b-valor col-valor"><?php echo $helper::format($colSegmento[$key]['valor']) ?></td>
						<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['valor'], $colSegmento[$key]['valor']));?></td>
						<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['valor'], $colSegmento[$key]['valor']));?>%</td>
				
						<?php if(isset($th2)):?>
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['total']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colSegmento[$key]['total']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['total'], $colSegmento[$key]['total']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['total'], $colSegmento[$key]['total']));?>%</td>
						<?php endif;?>
						
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['valor_ano_anterior']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colSegmento[$key]['valor_ano_anterior']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['valor_ano_anterior'], $colSegmento[$key]['valor_ano_anterior']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['valor_ano_anterior'], $colSegmento[$key]['valor_ano_anterior']));?>%</td>
						
						<?php if(isset($th4)):?>
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['total_ano_anterior']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colSegmento[$key]['total_ano_anterior']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['total_ano_anterior'], $colSegmento[$key]['total_ano_anterior']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['total_ano_anterior'], $colSegmento[$key]['total_ano_anterior']));?>%</td>
						<?php endif;?>
					</tr>
		<?php 
					$i++;
				endforeach;
			endif;
		?>
	</tbody>
</table>


<script type="text/javascript">
	//google.setOnLoadCallback(drawChart);
	function drawChart() {
        /*var data = google.visualization.arrayToDataTable([
          ['ID', 'Life Expectancy', 'Fertility Rate', 'Region'],
          ['CAN',    80.66,              1.67,      'CAN'],
          ['DEU',    79.84,              1.36,      'DEU'],
          ['DNK',    78.6,               1.84,      'DNK'],
          ['EGY',    72.73,              2.78,      'EGY'],
          ['GBR',    80.05,              2,         'GBR'],
          ['IRN',    72.49,              1.7,       'IRN'],
          ['IRQ',    68.09,              4.77,      'IRQ'],
          ['ISR',    81.55,              2.96,      'ISR'],
          ['RUS',    68.6,               1.54,      'RUS'],
          ['USA',    78.09,              2.05,      'USA']
        ]);*/


		
        

        var data = google.visualization.arrayToDataTable([
         	['CVM', '<?php echo $grafico_x_title ?>', '<?php echo $grafico_y_title ?>', 'Nome'],
		<?php 
		foreach ($grafico_res as $graf) {
			echo "['".$graf['cvm']."',".$graf['x'].",".$graf['y'].", '".$graf['nome']."'],";
		}
		?>
		]);
		
        

        var options = {
          title: 'Grafico',
          hAxis: {title: '<?php echo $grafico_x_title ?>'},
          vAxis: {title: '<?php echo $grafico_y_title ?>'},
          bubble: {textStyle: {fontSize: 11}}
        };

        var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
        chart.draw(data, options);
	}
	drawChart();
</script>