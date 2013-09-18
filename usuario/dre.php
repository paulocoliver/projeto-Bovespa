<?php
include_once 'auth.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/class/Documento/DocumentoUsuario.php';

$documento = new DocumentoUsuario();
$documento->newDoc($auth->getUsuario('id_usuario_empresa'), 1);

$colunas = $documento->getColunas();

$titlePage = 'DRE';
include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/header.php';
?>

<script type="text/javascript">
$(document).ready(function(){
	$('.money').mask("#.##0,00", {reverse: true, maxlength: false});
});
</script>

<style>
<!--
@import url("/include/css/usuario/dre.css");
-->
</style>
<div class="container">

	<a id="adicionar-dre" href="/usuario/index.php">Retornar</a>
	<div style="cler:both"></div>
	
	<form action="" method="post" id="form_dre">
		<table id="lista_dre">
			<tbody>
				<tr>
					<th style="width:100px;">Código</th>
					<th>Descricao</th>
					<th style="width:100px;">Valor</th>
				</tr>
				<?php 
					if(is_array($colunas)):
						$i=0;
						foreach ($colunas AS $coluna) :
				?>
							<tr style="background-color:<?php echo $i%2==0?'white':'#f5f5f5';?>">
								<td><?php echo utf8_encode($coluna['codigo'])?></td>
								<td><?php echo utf8_encode($coluna['descricao'])?></td>
								<td>
									<input placeHolder="R$0,00" type="text" class="money" name="<?php echo $coluna['id_coluna']?>" value="<?php echo $coluna['valor']?>"/>
								</td>
							</tr>
				<?php 
							$i++;
						endforeach;
					endif;
				?>
			</tbody>
		</table>
		<button type="submit" class="salvar">Gravar</button>
	</form>
</div>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/include/layout/footer.php';?>
