<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/BuscarDocumento.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Helper/Function.php';

$id_link  = !empty($_GET['id_link']) ? $_GET['id_link'] : null;

$buscarDoc = new BuscarDocumento();
$documento = $buscarDoc->getDocumento($id_link);

$colunas = $documento->getColunas();

$mes = substr($documento->getData(),5,2);
$ano = substr($documento->getData(),0,4);

switch($mes):
	case '06' : 
		$th1 = "01/04/$ano à 30/06/$ano";
		$th2 = "01/01/$ano à 30/06/$ano";
		$th3 = "01/04/".($ano-1)." à 30/06/".($ano-1);
		$th4 = "01/01/".($ano-1)." à 30/06/".($ano-1);
	break;
	case '09' : 
		$th1 = "01/07/$ano à 30/09/$ano";
		$th2 = "01/01/$ano à 30/09/$ano";
		$th3 = "01/07/".($ano-1)." à 30/09/".($ano-1);
		$th4 = "01/01/".($ano-1)." à 30/09/".($ano-1);
	break;
	default : 
		$th1 = "01/01/$ano à 31/03/$ano";
		$th3 = "01/01/".($ano-1)." à 30/03/".($ano-1);
	break;	
endswitch;
?>

<div id="myTabContent" class="tab-content">
	<table class="table table-bordered table-striped">
		<tbody>
			<tr>
				<th colspan="6" style="text-align: center;"><?php echo utf8_encode($documento->getDescricao()); ?></th>
			</tr>
			<tr>
				<th style="text-align: center;">Código da Conta</th>
				<th style="text-align: center;">Descrição da Conta</th>
				<th style="text-align: center;"><?php echo $th1?></th>
				<?php if(isset($th2)):?>
					<th style="text-align: center;"><?php echo $th2?></th>
				<?php endif;?>
				
				<th style="text-align: center;"><?php echo $th3?></th>
				
				<?php if(isset($th4)):?>
					<th style="text-align: center;"><?php echo $th4?></th>
				<?php endif;?>
			</tr>
			
<?php foreach ($colunas AS $coluna): ?>
			<tr>
				<td><?php echo $coluna['codigo'] ?></td>
				<td><?php echo utf8_encode($coluna['descricao']) ?></td>
				<td class="col-valor"><?php echo Helper_Function::format($coluna['valor']) ?></td>
				
				<?php if(isset($th2)):?>
					<td class="col-valor"><?php echo Helper_Function::format($coluna['total']) ?></td>
				<?php endif;?>
				
				<td class="col-valor"><?php echo Helper_Function::format($coluna['valor_ano_anterior']) ?></td>
				
				<?php if(isset($th4)):?>
					<td class="col-valor"><?php echo Helper_Function::format( $coluna['total_ano_anterior']) ?></td>
				<?php endif;?>
			</tr>
<?php endforeach; ?>

		</tbody>
	</table>
</div>
<br /><br />
