<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario();
$lista = $documento->select(null,$auth->getUsuario('id_usuario_empresa'));

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
	<div style="cler:both"></div>
	
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
							<td><?php echo $item['id_documento_usuario_empresa']?></td>
							<td><?php echo $item['descricao']?></td>
							<td><?php echo $item['data']?></td>
							<td>
								<a href="#">Editar</a>
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
