<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/BuscarDocumento.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Auth/Usuario.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Bovespa/Link.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Helper/Function.php';

try{
	$helper = new Helper_Function();
	$id_documento_usuario = $_GET['documento_usuario'];
	$cvm = $_GET['cvm'];
	
	if(empty($id_documento_usuario)||empty($cvm)){
		die("id_documento ou cvm nao encontrado");	
	}
	$auth = new Auth_Usuario();
	$docUsuario = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
	$docUsuario->loadDoc($id_documento_usuario);
	
	$link = new Link();
	$link = $link->getLinkByCVM($cvm, $docUsuario->getData());
	if($link == null){
		throw new Exception("A empresa não contém registro de dres para esta data.");
	}
	$buscarDoc  = new BuscarDocumento();
	$docBovespa = $buscarDoc->getDocumento($link->id_link);
	
	$colBovespa = $docBovespa->getColunas();
	$colUsuario = $docUsuario->getColunas();
	
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
						<td class="b-valor col-valor"><?php echo $helper::format($colBovespa[$key]['valor']) ?></td>
						<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['valor'], $colBovespa[$key]['valor']));?></td>
						<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['valor'], $colBovespa[$key]['valor']));?>%</td>
				
						<?php if(isset($th2)):?>
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['total']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colBovespa[$key]['total']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['total'], $colBovespa[$key]['total']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['total'], $colBovespa[$key]['total']));?>%</td>
						<?php endif;?>
						
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['valor_ano_anterior']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colBovespa[$key]['valor_ano_anterior']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['valor_ano_anterior'], $colBovespa[$key]['valor_ano_anterior']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['valor_ano_anterior'], $colBovespa[$key]['valor_ano_anterior']));?>%</td>
						
						<?php if(isset($th4)):?>
							<td class="u-valor col-valor"><?php echo $helper::format($colUsuario[$key]['total_ano_anterior']) ?></td>
							<td class="b-valor col-valor"><?php echo $helper::format($colBovespa[$key]['total_ano_anterior']) ?></td>
							<td class="diff col-valor"><?php echo $helper::format($helper::calcDiff($colUsuario[$key]['total_ano_anterior'], $colBovespa[$key]['total_ano_anterior']));?></td>
							<td class="diff-per col-valor"><?php echo $helper::format($helper::calcDiffPer($colUsuario[$key]['total_ano_anterior'], $colBovespa[$key]['total_ano_anterior']));?>%</td>
						<?php endif;?>
					</tr>
		<?php 
					$i++;
				endforeach;
			endif;
		?>
	</tbody>
</table>