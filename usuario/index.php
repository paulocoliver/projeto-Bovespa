<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario($auth->getUsuario('id_usuario_empresa'));
$lista = $documento->select();

$titlePage = 'Minha Empresa';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>

<style>
<!--
@import url("/include/css/usuario/index.css");
-->
</style>
<div class="container">
	
	<a id="adicionar-dre" href="/usuario/dre.php">Adicionar DRE</a>
	<div style="clear:both"></div>
	
	<?php //include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/menu.php';?>
		
	
		<table id="lista_dre">
			<tbody>
				<tr>
					<th style="width:100px;">Código</th>
					<th>Descricao</th>
					<th style="width:100px;">Data</th>
					<th style="width:100px;">Ação</th>
				</tr>
				<?php 
					if(is_array($lista)):
						$i=0;
						foreach ($lista AS $item) :
				?>
							<tr style="background-color:<?php echo $i%2==0?'white':'#f5f5f5';?>">
								<td><?php echo sprintf('%05d',$item['id_documento_usuario_empresa'])?></td>
								<td><?php echo utf8_encode($item['descricao'])?></td>
								<td><?php echo $item['data']?></td>
								<td style="text-align:center;">
									<a href="/usuario/dre.php?id=<?php echo $item['id_documento_usuario_empresa']?>">Editar</a>
								</td>
							</tr>
				<?php 
							$i++;
						endforeach;
					endif;
				?>
			</tbody>
		</table>
	
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>
